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

        // Cek apakah sudah ada pembayaran yang dikonfirmasi (cicilan 1 pun sudah cukup)
        $sudahBayar = BuktiPembayaran::where('mhsnobp', $nobp)
            ->where('sppsem', $semId)
            ->where('status', 'dikonfirmasi')
            ->exists();

        // Fallback: cek registrasi penuh (lunas) jika tidak ada bukti pembayaran
        if (!$sudahBayar) {
            $sudahBayar = Registrasi::where('regmhsnobp', $nobp)
                ->where('regsem', $semId)
                ->exists();
        }

        $bisaIsiKrs = $sudahBayar;

        return response()->json([
            'success' => true,
            'data'    => [
                'semester'         => $krsList->first()?->semester?->semnama ?? '-',
                'total_sks'        => $totalSks,
                'max_sks'          => 24,
                'bisa_isi_krs'     => $bisaIsiKrs,
                'pesan_pembayaran' => $bisaIsiKrs ? null : 'Anda belum melakukan pembayaran SPP untuk semester ini. Silakan upload bukti pembayaran terlebih dahulu sebelum mengisi KRS.',
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

        // Fallback: cek registrasi penuh (lunas)
        if (!$sudahBayar) {
            $sudahBayar = Registrasi::where('regmhsnobp', $nobp)
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
        $jadwal = $krs->kelas?->jadwalList?->first();
        $mk     = $krs->kelas?->kurikulum?->mataKuliah;

        return [
            'krs_id'       => $krs->krsid,
            'kode_kelas'   => $krs->kelas?->kelaskode . ' (' . $krs->kelas?->kelaslabel . ')',
            'kode_mk'      => $mk?->mtkid,
            'nama_mk'      => $mk?->mtknama,
            'sks'          => $mk?->mtksks,
            'dosen'        => $jadwal?->dosen?->nama_lengkap,
            'hari'         => $jadwal?->jadwalhari ?? '-',
            'ruangan'      => $jadwal?->ruang?->runama ?? '-',
            'status_krs'   => $krs->status_krs,
            'nilai'        => $krs->krsnilai,
            'bobot'        => $krs->krsbobot,
            'keterangan'   => $krs->ket_nilai,
        ];
    }
}
