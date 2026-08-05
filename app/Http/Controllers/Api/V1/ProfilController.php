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

        $biayaSetting = SettingBiaya::forMahasiswa($mahasiswa);

        $data = [
            'no_bp'              => $mahasiswa->mhsnobp,
            'nik'                => $mahasiswa->mhsnik,
            'nama'               => $mahasiswa->mhsnama,
            'email'              => $mahasiswa->mhsemail,
            'prodi'              => $mahasiswa->prodi?->prodinama,
            'angkatan'           => $mahasiswa->mhsangkatan,
            'tahun_masuk'        => $mahasiswa->mhsangkatan,
            'semester_awal_masuk'=> $mahasiswa->mhssemidmasuk,
            'tempat_lahir'       => $mahasiswa->mhstmplhr,
            'tanggal_lahir'      => $mahasiswa->mhstgllahir?->format('d F Y'),
            'jenis_kelamin'      => $mahasiswa->mhsjkl === 'L' ? 'Laki-Laki' : 'Perempuan',
            'alamat'             => $mahasiswa->mhsalamat,
            'telp'               => $mahasiswa->mhstelp,
            'agama'              => $mahasiswa->agama?->agamanama,
            'nama_ayah'          => $mahasiswa->mhsortu,
            'nama_ibu'           => $mahasiswa->mhsibu,
            'jalur'              => $mahasiswa->jalur?->jalurnama,
            'kelas_biaya'        => $this->kelasBiayaLabel($mahasiswa->mhskel),
            'biaya_kuliah'       => $biayaSetting?->biaya,
            'status'             => $mahasiswa->stat?->statnama,
        ];

        return response()->json([
            'success' => true,
            'data'    => $data,
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
