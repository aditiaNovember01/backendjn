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
     * Daftar kelas tersedia untuk semester aktif.
     * Filter: ?semester=2 (semester MK ke berapa)
     */
    public function index(Request $request): JsonResponse
    {
        $nobp      = $request->user()->mhsnobp;
        $mahasiswa = Mahasiswa::findOrFail($nobp);
        $semId     = Setting::semesterAktif();

        $query = Kelas::with([
            'kurikulum.mataKuliah',
            'jadwalList.dosen',
            'jadwalList.ruang',
            'angkatanList',
            'semester',
        ])
        ->where('kelassem', $semId)
        ->where('kelasprodiid', $mahasiswa->mhsprodiid);

        // Filter semester MK (semester ke berapa di kurikulum)
        if ($request->filled('semester')) {
            $query->whereHas('kurikulum', function ($q) use ($request) {
                $q->where('kursem', $request->semester);
            });
        }

        $kelasList = $query->get();

        // Filter kelas yang diizinkan untuk angkatan mahasiswa
        $kelasList = $kelasList->filter(function ($kelas) use ($mahasiswa) {
            // Jika tidak ada setting angkatan, semua boleh ambil
            if ($kelas->angkatanList->isEmpty()) {
                return true;
            }
            return $kelas->angkatanList
                ->pluck('kelasangangkatan')
                ->contains($mahasiswa->mhsangkatan);
        });

        // Kelompokkan per semester MK
        $grouped = $kelasList->groupBy(fn($k) => $k->kurikulum?->kursem ?? 0);

        $result = $grouped->map(function ($items, $sem) {
            return [
                'semester_mk' => $sem,
                'kelas'       => $items->values()->map(fn($k) => $this->formatKelas($k)),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }

    private function formatKelas(Kelas $kelas): array
    {
        $jadwal = $kelas->jadwalList->first();
        $mk     = $kelas->kurikulum?->mataKuliah;
        $jml    = $kelas->krsList()->where('krshapus', 0)->count();

        return [
            'kelas_id'          => $kelas->kelasid,
            'kode_kelas'        => $kelas->kelaskode . ' [' . $kelas->kelaslabel . ']',
            'kode_mk'           => $mk?->mtkid,
            'nama_mk'           => $mk?->mtknama,
            'sks'               => $mk?->mtksks,
            'semester_mk'       => $kelas->kurikulum?->kursem,
            'kapasitas'         => $kelas->kelasmax,
            'jumlah_mahasiswa'  => $jml,
            'is_penuh'          => $jml >= $kelas->kelasmax,
            'dosen'             => $jadwal?->dosen?->nama_lengkap,
            'hari'              => $jadwal?->jadwalhari ?? '-',
            'ruangan'           => $jadwal?->ruang?->runama ?? '-',
            'keterangan'        => $kelas->kelasket,
        ];
    }
}
