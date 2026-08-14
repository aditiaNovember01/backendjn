<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Krs;
use App\Models\Mahasiswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    /**
     * Histori nilai semua semester.
     */
    public function index(Request $request): JsonResponse
    {
        $nobp = $request->user()->mhsnobp;

        $krsList = Krs::with([
            'kelas.kurikulum.mataKuliah',
            'kelas.jadwalList.dosen',
            'semester',
        ])
        ->where('krsmhsnobp', $nobp)
        ->where('krshapus', 0)
        ->whereNotNull('krsnilai')
        ->where('krsnilai', '!=', '')
        ->orderBy('krssem')
        ->get();

        // Kelompokkan per semester
        $grouped = $krsList->groupBy('krssem');

        $semesters = $grouped->map(function ($items, $semId) {
            $totalSks  = $items->sum(fn($k) => $k->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0);
            $totalMutu = $items->sum(fn($k) => ($k->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0) * ($k->krsbobot ?? 0));
            $ip        = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0;

            return [
                'sem_id'      => $semId,
                'semester'    => $items->first()?->semester?->semnama ?? '-',
                'total_sks'   => $totalSks,
                'total_mutu'  => $totalMutu,
                'ip'          => $ip,
                'matakuliah'  => $items->values()->map(fn($k) => $this->formatNilaiItem($k)),
            ];
        })->values();

        // Hitung IPK keseluruhan
        $allSks   = $krsList->sum(fn($k) => $k->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0);
        $allMutu  = $krsList->sum(fn($k) => ($k->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0) * ($k->krsbobot ?? 0));
        $ipk      = $allSks > 0 ? round($allMutu / $allSks, 2) : 0;

        $mahasiswa = Mahasiswa::with('prodi')->findOrFail($nobp);

        return response()->json([
            'success' => true,
            'data'    => [
                'no_bp'       => $nobp,
                'nama'        => $mahasiswa->mhsnama,
                'prodi'       => $mahasiswa->prodi?->prodinama,
                'total_sks'   => $allSks,
                'ipk'         => $ipk,
                'semesters'   => $semesters,
            ],
        ]);
    }

    /**
     * Ringkasan: IP per semester + IPK.
     */
    public function summary(Request $request): JsonResponse
    {
        $nobp = $request->user()->mhsnobp;

        $krsList = Krs::with(['kelas.kurikulum.mataKuliah', 'kelas.jadwalList.dosen', 'semester'])
            ->where('krsmhsnobp', $nobp)
            ->where('krshapus', 0)
            ->whereNotNull('krsnilai')
            ->where('krsnilai', '!=', '')
            ->get();

        $grouped = $krsList->groupBy('krssem');

        $summary = $grouped->map(function ($items, $semId) {
            $totalSks  = $items->sum(fn($k) => $k->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0);
            $totalMutu = $items->sum(fn($k) => ($k->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0) * ($k->krsbobot ?? 0));
            $ip        = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0;

            return [
                'sem_id'    => $semId,
                'semester'  => $items->first()?->semester?->semnama ?? '-',
                'total_sks' => $totalSks,
                'ip'        => $ip,
            ];
        })->values();

        $allSks  = $krsList->sum(fn($k) => $k->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0);
        $allMutu = $krsList->sum(fn($k) => ($k->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0) * ($k->krsbobot ?? 0));
        $ipk     = $allSks > 0 ? round($allMutu / $allSks, 2) : 0;

        return response()->json([
            'success' => true,
            'data'    => [
                'total_sks_lulus' => $allSks,
                'ipk'             => $ipk,
                'per_semester'    => $summary,
            ],
        ]);
    }

    private function formatNilaiItem(Krs $krs): array
    {
        $mk            = $krs->kelas?->kurikulum?->mataKuliah;
        $sks           = $mk?->mtksks ?? 0;
        $jadwalPertama = $krs->kelas?->jadwalList?->first();

        return [
            'krs_id'      => $krs->krsid,
            'kode_kelas'  => $krs->kelas?->kelaskode,
            'kode_mk'     => $mk?->mtkid,
            'nama_mk'     => $mk?->mtknama,
            'sks'         => $sks,
            'nilai'       => $krs->krsnilai,
            'bobot'       => $krs->krsbobot,
            'mutu'        => $sks * ($krs->krsbobot ?? 0),
            'keterangan'  => $krs->ket_nilai,
            'dosen'       => $jadwalPertama?->dosen?->nama_lengkap ?? '-',
            'jumlah_absen'=> $krs->krsjmlabsen,
        ];
    }
}
