<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Tabel konversi ID jam → waktu HH:mm
     * Standar kampus: 1 sesi = 50 menit, mulai 07:00
     * ID 1=07:00, 2=07:50, 3=08:40, 4=09:30, dst.
     */
    private const JAM_MAP = [
        1  => '07:00',  2  => '07:50',  3  => '08:40',
        4  => '09:30',  5  => '10:20',  6  => '11:10',
        7  => '13:00',  8  => '13:50',  9  => '14:40',
        10 => '15:30', 11 => '16:20', 12 => '17:10',
        13 => '18:00', 14 => '18:50', 15 => '19:40',
    ];

    /**
     * Konversi ID jam ke waktu selesai (jam akhir + 50 menit).
     */
    private function jamSelesai(?int $jamId): ?string
    {
        if (!$jamId || !isset(self::JAM_MAP[$jamId])) return null;
        $parts  = explode(':', self::JAM_MAP[$jamId]);
        $menit  = intval($parts[0]) * 60 + intval($parts[1]) + 50;
        return str_pad(intdiv($menit, 60), 2, '0', STR_PAD_LEFT)
             . ':' . str_pad($menit % 60, 2, '0', STR_PAD_LEFT);
    }

    private function formatJadwal($jadwal): array
    {
        $jamMulai   = self::JAM_MAP[$jadwal->jadwaljamidawal]  ?? null;
        $jamSelesai = $this->jamSelesai($jadwal->jadwaljamidakhir);

        return [
            'hari'        => $jadwal->jadwalhari ?? '-',
            'jam_mulai'   => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'jam_label'   => $jamMulai && $jamSelesai ? "$jamMulai – $jamSelesai" : '-',
            'ruangan'     => $jadwal->ruang?->runama ?? '-',
            'dosen'       => $jadwal->dosen?->nama_lengkap ?? '-',
        ];
    }

    /**
     * Daftar kelas tersedia untuk semester aktif.
     * Filter: prodi + angkatan mahasiswa login.
     *
     * Query params:
     *   ?semester=7     → filter semester MK ke-berapa (1-8)
     *   ?sem_id=20252   → override semester aktif
     */
    public function index(Request $request): JsonResponse
    {
        $nobp      = $request->user()->mhsnobp;
        $mahasiswa = Mahasiswa::findOrFail($nobp);
        $semId     = $request->query('sem_id', Setting::semesterAktif());

        $query = Kelas::with([
            'kurikulum.mataKuliah',
            'jadwalList.dosen',
            'jadwalList.ruang',
            'angkatanList',
            'semester',
        ])
        ->where('kelassem', $semId)
        ->where('kelasprodiid', $mahasiswa->mhsprodiid);

        if ($request->filled('semester')) {
            $query->whereHas('kurikulum', fn($q) => $q->where('kursem', $request->semester));
        }

        $kelasList = $query->get()->filter(function ($kelas) use ($mahasiswa) {
            if ($kelas->angkatanList->isEmpty()) return true;
            return $kelas->angkatanList
                ->pluck('kelasangangkatan')
                ->contains($mahasiswa->mhsangkatan);
        });

        $grouped = $kelasList
            ->groupBy(fn($k) => $k->kurikulum?->kursem ?? 0)
            ->sortKeys()
            ->map(fn($items, $sem) => [
                'semester_mk' => (int) $sem,
                'kelas'       => $items->values()->map(fn($k) => $this->formatKelas($k)),
            ])->values();

        return response()->json(['success' => true, 'data' => $grouped]);
    }

    private function formatKelas(Kelas $kelas): array
    {
        $mk          = $kelas->kurikulum?->mataKuliah;
        $jumlahMhs   = $kelas->krsList()->where('krshapus', 0)->count();
        $jadwalFirst = $kelas->jadwalList->first();
        $jadwalAll   = $kelas->jadwalList->map(fn($j) => $this->formatJadwal($j))->values();

        $jamMulai   = self::JAM_MAP[$jadwalFirst?->jadwaljamidawal]  ?? null;
        $jamSelesai = $this->jamSelesai($jadwalFirst?->jadwaljamidakhir);

        return [
            'kelas_id'         => $kelas->kelasid,
            'kode_kelas'       => $kelas->kelaskode,
            'label_kelas'      => $kelas->kelaslabel,
            'kode_mk'          => $mk?->mtkid,
            'nama_mk'          => $mk?->mtknama,
            'nama_mk_inggris'  => $mk?->mtkasing,
            'sks'              => $mk?->mtksks,
            'semester_mk'      => $kelas->kurikulum?->kursem,
            'kapasitas'        => $kelas->kelasmax,
            'jumlah_mahasiswa' => $jumlahMhs,
            'is_penuh'         => $jumlahMhs >= $kelas->kelasmax,
            // Jadwal ringkas (jadwal pertama) — siap pakai di React Native
            'hari'             => $jadwalFirst?->jadwalhari ?? '-',
            'jam_mulai'        => $jamMulai,
            'jam_selesai'      => $jamSelesai,
            'jam_label'        => $jamMulai && $jamSelesai ? "$jamMulai – $jamSelesai" : '-',
            'ruangan'          => $jadwalFirst?->ruang?->runama ?? '-',
            'dosen'            => $jadwalFirst?->dosen?->nama_lengkap ?? '-',
            // Detail semua jadwal
            'jadwal'           => $jadwalAll,
            'keterangan'       => $kelas->kelasket,
            'prasyarat'        => array_values(array_filter([
                $kelas->kurikulum?->kurmtkidprasyarat,
                $kelas->kurikulum?->kurmtkidprasyarat2,
                $kelas->kurikulum?->kurmtkidprasyarat3,
            ])),
        ];
    }
}
