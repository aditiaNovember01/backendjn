<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = [
        'judul', 'isi', 'tgl_publish', 'aktif', 'user_id',
    ];

    protected $casts = [
        'aktif'       => 'boolean',
        'tgl_publish' => 'date',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Scope: hanya yang aktif & sudah publish ───────────────
    public function scopePublished($query)
    {
        return $query->where('aktif', true)
            ->where(function ($q) {
                $q->whereNull('tgl_publish')
                  ->orWhere('tgl_publish', '<=', now()->toDateString());
            });
    }
}
