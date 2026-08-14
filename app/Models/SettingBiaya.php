<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingBiaya extends Model
{
    protected $table    = 'settingbiaya';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'prodi', 'angkatan', 'kelas',
        'biaya',        // biaya penuh per semester
        'biaya1',       // cicilan pertama (50%)
        'biaya2',       // cicilan kedua (sisa)
        'pembangunan1', // uang pembangunan tahun 1
        'pembangunan2', // uang pembangunan tahun 2
        'pembangunan3', // uang pembangunan tahun 3
        'pembangunan4', // uang pembangunan tahun 4
        'orientasi',    // biaya orientasi/PKKMB
    ];

    protected $casts = [
        'biaya'        => 'float',
        'biaya1'       => 'float',
        'biaya2'       => 'float',
        'pembangunan1' => 'float',
        'pembangunan2' => 'float',
        'pembangunan3' => 'float',
        'pembangunan4' => 'float',
        'orientasi'    => 'float',
        'angkatan'     => 'integer',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi', 'prodiid');
    }

    /**
     * Ambil setting biaya untuk mahasiswa tertentu.
     * Cocokkan berdasarkan prodi + angkatan + kelas (R/E/K).
     */
    public static function forMahasiswa(Mahasiswa $mhs): ?self
    {
        return self::where('prodi', $mhs->mhsprodiid)
            ->where('angkatan', $mhs->mhsangkatan)
            ->where('kelas', $mhs->mhskel)
            ->first();
    }

    /**
     * Hitung tahun ke berapa mahasiswa berdasarkan angkatan dan semester masuk.
     * Untuk menentukan biaya pembangunan yang berlaku.
     */
    public function getPembangunanByTahun(int $tahunKe): float
    {
        return match ($tahunKe) {
            1       => (float) ($this->pembangunan1 ?? 0),
            2       => (float) ($this->pembangunan2 ?? 0),
            3       => (float) ($this->pembangunan3 ?? 0),
            4       => (float) ($this->pembangunan4 ?? 0),
            default => 0,
        };
    }

    /**
     * Kembalikan semua komponen biaya sebagai array terstruktur.
     */
    public function toDetailArray(): array
    {
        return [
            'biaya_penuh'   => $this->biaya,
            'cicilan_1'     => $this->biaya1,
            'cicilan_2'     => $this->biaya2,
            'pembangunan_1' => $this->pembangunan1,
            'pembangunan_2' => $this->pembangunan2,
            'pembangunan_3' => $this->pembangunan3,
            'pembangunan_4' => $this->pembangunan4,
            'orientasi'     => $this->orientasi,
        ];
    }
}
