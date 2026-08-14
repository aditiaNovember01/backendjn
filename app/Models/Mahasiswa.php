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
        'mhstelp', 'mhstahunkur', 'mhskel', 'mhsasalsekolah',
        'mhssemidmasuk', 'mhsnik', 'mhsnisn', 'mhsumberbiayaid',
        'mhsemail', 'mhstgllahir', 'mhskelurahan', 'mhskecamatan',
    ];

    protected $casts = [
        'mhsangkatan' => 'integer',
        'mhstgllahir' => 'date',
        'mhstahunkur' => 'integer',
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

    // ── Helper: setting biaya untuk mahasiswa ini ──────────────
    public function getSettingBiaya(): ?SettingBiaya
    {
        return SettingBiaya::forMahasiswa($this);
    }

    /**
     * Hitung tahun ke berapa mahasiswa saat ini.
     * Berdasarkan selisih angkatan dengan tahun akademik berjalan.
     */
    public function getTahunKe(): int
    {
        $semAktifId  = Setting::semesterAktif();
        $tahunSemAkt = (int) substr((string) $semAktifId, 0, 4);
        $tahunKe     = $tahunSemAkt - $this->mhsangkatan + 1;
        return max(1, min(4, $tahunKe));
    }

    /**
     * Ambil dosen PA dari jadwal KRS semester ini.
     */
    public function getDosenPA(): ?Dosen
    {
        $semId = Setting::semesterAktif();
        $krs   = $this->krsList()
            ->where('krssem', $semId)
            ->where('krshapus', 0)
            ->with('kelas.jadwalList.dosen')
            ->first();

        return $krs?->kelas?->jadwalList?->first()?->dosen;
    }
}
