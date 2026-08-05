<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingBiaya extends Model
{
    protected $table    = 'settingbiaya';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'prodi', 'angkatan', 'kelas',
        'biaya', 'biaya1', 'biaya2',
        'pembangunan1', 'pembangunan2', 'pembangunan3', 'pembangunan4',
        'orientasi',
    ];

    protected $casts = [
        'biaya'  => 'float',
        'biaya1' => 'float',
        'biaya2' => 'float',
    ];

    /**
     * Ambil biaya untuk mahasiswa tertentu.
     */
    public static function forMahasiswa(Mahasiswa $mhs): ?self
    {
        return self::where('prodi', $mhs->mhsprodiid)
            ->where('angkatan', $mhs->mhsangkatan)
            ->where('kelas', $mhs->mhskel)
            ->first();
    }
}
