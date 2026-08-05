<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    protected $table      = 'mahasiswa';
    protected $primaryKey = 'mhsnobp';
    protected $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'mhsnobp', 'mhsnama', 'mhsalamat', 'mhsangkatan',
        'mhsprodiid', 'mhsagamaid', 'mhsjalurid', 'mhsstatid',
        'mhstgllhr', 'mhstmplhr', 'mhsjkl', 'mhsortu', 'mhsibu',
        'mhstelp', 'mhstahunkur', 'mhskel', 'mhssemidmasuk',
        'mhsnik', 'mhsemail', 'mhstgllahir',
    ];

    protected $casts = [
        'mhsangkatan' => 'integer',
        'mhstgllahir' => 'date',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'mhsprodiid', 'prodiid');
    }

    public function agama(): BelongsTo
    {
        return $this->belongsTo(Agama::class, 'mhsagamaid', 'agamaid');
    }

    public function jalur(): BelongsTo
    {
        return $this->belongsTo(Jalur::class, 'mhsjalurid', 'jalurid');
    }

    public function stat(): BelongsTo
    {
        return $this->belongsTo(Stat::class, 'mhsstatid', 'statid');
    }

    public function krsList(): HasMany
    {
        return $this->hasMany(Krs::class, 'krsmhsnobp', 'mhsnobp');
    }

    public function sppList(): HasMany
    {
        return $this->hasMany(Spp::class, 'sppmhsnobp', 'mhsnobp');
    }

    public function registrasiList(): HasMany
    {
        return $this->hasMany(Registrasi::class, 'regmhsnobp', 'mhsnobp');
    }

    public function buktiPembayaranList(): HasMany
    {
        return $this->hasMany(BuktiPembayaran::class, 'mhsnobp', 'mhsnobp');
    }

    // ── Accessor: nama lengkap title-case ──────────────────────
    public function getNamaAttribute(): string
    {
        return ucwords(strtolower($this->mhsnama));
    }

    // ── Helper: biaya kuliah dari settingbiaya ─────────────────
    public function getBiayaKuliah(int $semId): ?float
    {
        return SettingBiaya::where('prodi', $this->mhsprodiid)
            ->where('angkatan', $this->mhsangkatan)
            ->where('kelas', $this->mhskel)
            ->value('biaya');
    }
}
