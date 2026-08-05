<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table      = 'kelas';
    protected $primaryKey = 'kelasid';
    public    $timestamps = false;

    protected $fillable = [
        'kelasid', 'kelaskode', 'kelaskurid', 'kelasprodiid',
        'kelassem', 'kelasmax', 'kelasnobpmin', 'kelasnobpmax',
        'kelaskel', 'kelaslabel', 'kelasket',
    ];

    protected $casts = [
        'kelasmax' => 'integer',
        'kelassem' => 'integer',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function kurikulum(): BelongsTo
    {
        return $this->belongsTo(Kurikulum::class, 'kelaskurid', 'kurid');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Sem::class, 'kelassem', 'semid');
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'kelasprodiid', 'prodiid');
    }

    public function jadwalList(): HasMany
    {
        return $this->hasMany(KelasJadwal::class, 'jadwalkelasid', 'kelasid');
    }

    public function angkatanList(): HasMany
    {
        return $this->hasMany(KelasAngkatan::class, 'kelasangkelasid', 'kelasid');
    }

    public function krsList(): HasMany
    {
        return $this->hasMany(Krs::class, 'krskelasid', 'kelasid');
    }

    // ── Accessor: jumlah mahasiswa terdaftar ───────────────────
    public function getJumlahMahasiswaAttribute(): int
    {
        return $this->krsList()->where('krshapus', 0)->count();
    }

    // ── Accessor: apakah kelas sudah penuh ────────────────────
    public function getIsPenuhAttribute(): bool
    {
        return $this->jumlah_mahasiswa >= $this->kelasmax;
    }
}
