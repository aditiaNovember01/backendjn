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
        if (! $path) return null;

        // Gunakan Storage::url() — bergantung pada APP_URL di .env
        // Pastikan APP_URL dan FILESYSTEM_DISK=public sudah diset benar di server
        return Storage::disk('public')->url($path);
    }

    public function getFileOriginalUrlAttribute(): ?string
    {
        if (! $this->file_path) return null;
        return Storage::disk('public')->url($this->file_path);
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
