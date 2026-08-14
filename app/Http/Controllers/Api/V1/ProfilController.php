<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\SettingBiaya;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $nobp = $request->user()->mhsnobp;

        $mahasiswa = Mahasiswa::with([
            'prodi',
            'agama',
            'jalur',
            'stat',
        ])->findOrFail($nobp);

        // Ambil setting biaya lengkap berdasarkan prodi + angkatan + kelas
        $biaya = SettingBiaya::forMahasiswa($mahasiswa);

        // Hitung tahun ke berapa untuk biaya pembangunan
        $tahunKe    = $mahasiswa->getTahunKe();
        $pembangunan = $biaya?->getPembangunanByTahun($tahunKe) ?? 0;

        // Dosen PA dari jadwal KRS semester aktif
        $dosenPA = $mahasiswa->getDosenPA();

        return response()->json([
            'success' => true,
            'data'    => [
                // Identitas
                'no_bp'               => $mahasiswa->mhsnobp,
                'nik'                 => $mahasiswa->mhsnik,
                'nama'                => $mahasiswa->mhsnama,
                'email'               => $mahasiswa->mhsemail,

                // Akademik
                'prodi'               => $mahasiswa->prodi?->prodinama,
                'angkatan'            => $mahasiswa->mhsangkatan,
                'tahun_kurikulum'     => $mahasiswa->mhstahunkur,
                'semester_awal_masuk' => $mahasiswa->mhssemidmasuk,
                'tahun_ke'            => $tahunKe,
                'dosen_pa'            => $dosenPA?->nama_lengkap,
                'status'              => $mahasiswa->stat?->statnama,

                // Pribadi
                'tempat_lahir'        => $mahasiswa->mhstmplhr,
                'tanggal_lahir'       => $mahasiswa->mhstgllahir?->format('d F Y'),
                'jenis_kelamin'       => $mahasiswa->mhsjkl === 'L' ? 'Laki-Laki' : 'Perempuan',
                'alamat'              => $mahasiswa->mhsalamat,
                'kelurahan'           => $mahasiswa->mhskelurahan,
                'kecamatan'           => $mahasiswa->mhskecamatan,
                'telp'                => $mahasiswa->mhstelp,
                'agama'               => $mahasiswa->agama?->agamanama,
                'asal_sekolah'        => $mahasiswa->mhsasalsekolah,

                // Orang tua
                'nama_ayah'           => $mahasiswa->mhsortu,
                'nama_ibu'            => $mahasiswa->mhsibu,

                // Jalur & kelas
                'jalur'               => $mahasiswa->jalur?->jalurnama,
                'kelas_biaya'         => $this->kelasBiayaLabel($mahasiswa->mhskel),
                'kelas_kode'          => $mahasiswa->mhskel,

                // Biaya kuliah
                'biaya'               => [
                    'spp_penuh'        => $biaya?->biaya,
                    'cicilan_1'        => $biaya?->biaya1,
                    'cicilan_2'        => $biaya?->biaya2,
                    'pembangunan'      => $pembangunan > 0 ? $pembangunan : null,
                    'orientasi'        => $biaya?->orientasi,
                    'tahun_ke'         => $tahunKe,
                ],
            ],
        ]);
    }

    private function kelasBiayaLabel(?string $kode): string
    {
        return match ($kode) {
            'R'     => 'Reguler',
            'E'     => 'Reguler Malam',
            'K'     => 'KIP Kuliah',
            default => $kode ?? '-',
        };
    }
}
