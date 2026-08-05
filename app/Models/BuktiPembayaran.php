<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BuktiPembayaran extends Model
{
    protected $table = 'bukti_pembayaran';

    protected $fillable = [
        'mhsnobp', 'sppsem', 'jumlah_bayar',
        'file_path', 'file_compressed',
        'status', 'catatan',
        'confirmed_by', 'confirmed_at',
    ];

    protected $casts = [
        'jumlah_bayar' => 'float',
        'confirmed_at' => 'datetime',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mhsnobp', 'mhsnobp');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Sem::class, 'sppsem', 'semid');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // ── Accessor: URL gambar (compressed jika ada) ─────────────
    public function getFileUrlAttribute(): ?string
    {
        $path = $this->file_compressed ?? $this->file_path;
        return $path ? Storage::disk('public')->url($path) : null;
    }

    public function getFileOriginalUrlAttribute(): ?string
    {
        return $this->file_path
            ? Storage::disk('public')->url($this->file_path)
            : null;
    }

    // ── Accessor: badge warna status ─────────────────────────
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'dikonfirmasi' => 'success',
            'ditolak'      => 'danger',
            default        => 'warning',
        };
    }

    // ── Scope filters ─────────────────────────────────────────
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDikonfirmasi($query)
    {
        return $query->where('status', 'dikonfirmasi');
    }
}
