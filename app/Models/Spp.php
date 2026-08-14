<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Spp extends Model
{
    protected $table      = 'spp';
    protected $primaryKey = 'sppid';
    public    $timestamps = false;

    protected $fillable = ['sppid', 'sppmhsnobp', 'sppsem', 'spptagihan'];

    protected $casts = [
        'spptagihan' => 'float',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'sppmhsnobp', 'mhsnobp');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Sem::class, 'sppsem', 'semid');
    }

    /**
     * Cek apakah sudah ada registrasi (konfirmasi pembayaran).
     */
    public function registrasi(): HasOne
    {
        return $this->hasOne(Registrasi::class, 'regmhsnobp', 'sppmhsnobp')
            ->where('regsem', $this->sppsem);
    }

    public function buktiPembayaran(): HasOne
    {
        return $this->hasOne(BuktiPembayaran::class, 'mhsnobp', 'sppmhsnobp')
            ->where('sppsem', $this->sppsem)
            ->latest();
    }

    // ── Accessor: status lunas ────────────────────────────────
    public function getStatusLunasAttribute(): string
    {
        $reg = Registrasi::lunas()
            ->where('regmhsnobp', $this->sppmhsnobp)
            ->where('regsem', $this->sppsem)
            ->first();
        return $reg ? 'Lunas' : 'Belum Lunas';
    }
}
