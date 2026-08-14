<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BuktiPembayaran;
use App\Models\Kelas;
use App\Models\KelasAngkatan;
use App\Models\Krs;
use App\Models\Registrasi;
use App\Models\Sem;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KrsController extends Controller
{
    /**
     * Lihat KRS semester aktif (atau semester tertentu via ?sem_id=).
     */
    public function index(Request $request): JsonResponse
    {
        $nobp  = $request->user()->mhsnobp;
        $semId = $request->query('sem_id', Setting::semesterAktif());

        $krsList = Krs::with([
            'kelas.kurikulum.mataKuliah',
            'kelas.jadwalList.dosen',
            'kelas.jadwalList.ruang',
            'semester',
        ])
        ->where('krsmhsnobp', $nobp)
        ->where('krssem', $semId)
        ->where('krshapus', 0)
        ->get();

        $totalSks = $krsList->sum(fn($k) => $k->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0);

        $items = $krsList->map(fn($krs) => $this->formatKrsItem($krs));

        // Cek pembayaran — cicilan 1 dikonfirmasi sudah cukup untuk isi KRS
        $sudahBayar = BuktiPembayaran::where('mhsnobp', $nobp)
            ->where('sppsem', $semId)
            ->where('status', 'dikonfirmasi')
            ->exists();

        // Fallback: cek registrasi lunas sungguhan (bukan AUTO)
        if (!$sudahBayar) {
            $sudahBayar = Registrasi::lunas()
                ->where('regmhsnobp', $nobp)
                ->where('regsem', $semId)
                ->exists();
        }

        // Dosen PA dari jadwal KRS
        $dosenPA = null;
        if ($krsList->isNotEmpty()) {
            $dosenPA = $krsList->first()?->kelas?->jadwalList?->first()?->dosen?->nama_lengkap;
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'semester'         => $krsList->first()?->semester?->semnama ?? '-',
                'sem_id'           => (int) $semId,
                'total_sks'        => $totalSks,
                'max_sks'          => 24,
                'dosen_pa'         => $dosenPA,
                'bisa_isi_krs'     => $sudahBayar,
                'pesan_pembayaran' => $sudahBayar ? null : 'Anda belum melakukan pembayaran SPP untuk semester ini. Silakan upload bukti pembayaran terlebih dahulu.',
                'items'            => $items,
            ],
        ]);
    }

    /**
     * Tambah KRS — ambil kelas.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'kelas_id' => ['required', 'integer', 'exists:kelas,kelasid'],
        ]);

        $nobp  = $request->user()->mhsnobp;
        $semId = Setting::semesterAktif();
        $sem   = Sem::find($semId);

        // Cek periode KRS
        if (! $sem?->isKrsOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Periode pengisian KRS belum dibuka atau sudah tutup.',
            ], 422);
        }

        $kelas = Kelas::findOrFail($request->kelas_id);

        // Cek apakah kelas untuk semester aktif
        if ($kelas->kelassem !== $semId) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak tersedia pada semester aktif.',
            ], 422);
        }

        // Cek kapasitas kelas
        if ($kelas->is_penuh) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas sudah penuh.',
            ], 422);
        }

        // Cek sudah mengambil kelas ini
        $existing = Krs::where('krsmhsnobp', $nobp)
            ->where('krskelasid', $request->kelas_id)
            ->where('krssem', $semId)
            ->where('krshapus', 0)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengambil kelas ini.',
            ], 422);
        }

        // Cek total SKS tidak melebihi 24
        $totalSks = Krs::where('krsmhsnobp', $nobp)
            ->where('krssem', $semId)
            ->where('krshapus', 0)
            ->with('kelas.kurikulum.mataKuliah')
            ->get()
            ->sum(fn($k) => $k->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0);

        $sksBaru = $kelas->kurikulum?->mataKuliah?->mtksks ?? 0;

        if (($totalSks + $sksBaru) > 24) {
            return response()->json([
                'success' => false,
                'message' => "Total SKS melebihi batas maksimal 24 SKS. SKS saat ini: {$totalSks}, SKS kelas ini: {$sksBaru}.",
            ], 422);
        }

        // Cek sudah ada pembayaran yang dikonfirmasi (cicilan 1 pun cukup untuk isi KRS)
        $sudahBayar = BuktiPembayaran::where('mhsnobp', $nobp)
            ->where('sppsem', $semId)
            ->where('status', 'dikonfirmasi')
            ->exists();

        // Fallback: cek registrasi lunas sungguhan (bukan AUTO)
        if (!$sudahBayar) {
            $sudahBayar = Registrasi::lunas()
                ->where('regmhsnobp', $nobp)
                ->where('regsem', $semId)
                ->exists();
        }

        if (!$sudahBayar) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan pembayaran SPP semester ini. Silakan upload dan tunggu konfirmasi bukti pembayaran sebelum mengisi KRS.',
            ], 422);
        }

        // Ambil atau buat record registrasi — krsregid wajib NOT NULL.
        // Mahasiswa yang sudah dikonfirmasi bukti bayar (cicilan) belum tentu
        // punya record di tabel registrasi, sehingga perlu di-create otomatis.
        $registrasi = Registrasi::firstOrCreate(
            [
                'regmhsnobp' => $nobp,
                'regsem'     => $semId,
            ],
            [
                'regjumlahbayar'  => 0,
                'regtanggalbayar' => now()->toDateString(),
                'reguserinput'    => $nobp,
                'regnobukti'      => 'AUTO-' . $nobp . '-' . $semId,
            ]
        );

        DB::transaction(function () use ($nobp, $request, $semId, $registrasi) {
            Krs::create([
                'krsregid'         => $registrasi->regid,
                'krsmhsnobp'       => $nobp,
                'krskelasid'       => $request->kelas_id,
                'krssem'           => $semId,
                'krshapus'         => 0,
                'krsapproved'      => 0,
                'krstanggalambil'  => now(),
                'krsuserambil'     => $nobp,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'KRS berhasil ditambahkan. Menunggu persetujuan Dosen PA.',
        ], 201);
    }

    /**
     * Batal ambil KRS.
     */
    public function destroy(Request $request, int $krsId): JsonResponse
    {
        $nobp = $request->user()->mhsnobp;
        $sem  = Sem::find(Setting::semesterAktif());

        if (! $sem?->isKrsOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Periode KRS sudah tutup. Tidak bisa membatalkan KRS.',
            ], 422);
        }

        $krs = Krs::where('krsid', $krsId)
            ->where('krsmhsnobp', $nobp)
            ->where('krshapus', 0)
            ->firstOrFail();

        $krs->update([
            'krshapus'        => 1,
            'krstanggalhapus' => now(),
            'krsuserhapus'    => $nobp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'KRS berhasil dibatalkan.',
        ]);
    }

    // ── Helper format item ─────────────────────────────────────
    private function formatKrsItem(Krs $krs): array
    {
        $mk          = $krs->kelas?->kurikulum?->mataKuliah;
        $jadwalList  = $krs->kelas?->jadwalList ?? collect();

        // Konversi ID jam ke HH:mm — standar kampus 1 sesi = 50 menit mulai 07:00
        $jamMap = [
            1=>'07:00', 2=>'07:50', 3=>'08:40', 4=>'09:30', 5=>'10:20', 6=>'11:10',
            7=>'13:00', 8=>'13:50', 9=>'14:40', 10=>'15:30', 11=>'16:20', 12=>'17:10',
            13=>'18:00', 14=>'18:50', 15=>'19:40',
        ];
        $jamSelesai = function(?int $id) use ($jamMap): ?string {
            if (!$id || !isset($jamMap[$id])) return null;
            [$h, $m] = explode(':', $jamMap[$id]);
            $total   = intval($h) * 60 + intval($m) + 50;
            return str_pad(intdiv($total,60),2,'0',STR_PAD_LEFT).':'.str_pad($total%60,2,'0',STR_PAD_LEFT);
        };

        // Format semua jadwal dengan waktu lengkap
        $jadwal = $jadwalList->map(function($j) use ($jamMap, $jamSelesai) {
            $mulai   = $jamMap[$j->jadwaljamidawal]  ?? null;
            $selesai = $jamSelesai($j->jadwaljamidakhir);
            return [
                'hari'        => $j->jadwalhari ?? '-',
                'jam_mulai'   => $mulai,
                'jam_selesai' => $selesai,
                'jam_label'   => $mulai && $selesai ? "$mulai – $selesai" : '-',
                'ruangan'     => $j->ruang?->runama ?? '-',
                'dosen'       => $j->dosen?->nama_lengkap ?? '-',
            ];
        })->values();

        $j1      = $jadwalList->first();
        $mulai1  = $jamMap[$j1?->jadwaljamidawal]  ?? null;
        $selesai1= $jamSelesai($j1?->jadwaljamidakhir);

        return [
            'krs_id'       => $krs->krsid,
            'kelas_id'     => $krs->krskelasid,
            'kode_kelas'   => $krs->kelas?->kelaskode,
            'label_kelas'  => $krs->kelas?->kelaslabel,
            'kode_mk'      => $mk?->mtkid,
            'nama_mk'      => $mk?->mtknama,
            'sks'          => $mk?->mtksks,
            'semester_mk'  => $krs->kelas?->kurikulum?->kursem,
            // Jadwal ringkas — siap pakai di React Native
            'hari'         => $j1?->jadwalhari ?? '-',
            'jam_mulai'    => $mulai1,
            'jam_selesai'  => $selesai1,
            'jam_label'    => $mulai1 && $selesai1 ? "$mulai1 – $selesai1" : '-',
            'ruangan'      => $j1?->ruang?->runama ?? '-',
            'dosen'        => $j1?->dosen?->nama_lengkap ?? '-',
            // Detail semua jadwal
            'jadwal'       => $jadwal,
            'status_krs'   => $krs->status_krs,
            'nilai'        => $krs->krsnilai ?: null,
            'bobot'        => $krs->krsbobot,
            'keterangan'   => $krs->ket_nilai,
            'jumlah_absen' => $krs->krsjmlabsen,
        ];
    }
}
