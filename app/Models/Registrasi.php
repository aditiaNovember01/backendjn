<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registrasi extends Model
{
    protected $table      = 'registrasi';
    protected $primaryKey = 'regid';
    public    $timestamps = false;

    const CREATED_AT = 'regtanggalinsert';
    const UPDATED_AT = null;

    protected $fillable = [
        'regid', 'regmhsnobp', 'regsem',
        'regjumlahbayar', 'regtanggalbayar',
        'reguserinput', 'regnobukti',
    ];

    protected $casts = [
        'regtanggalbayar'   => 'date',
        'regjumlahbayar'    => 'integer',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'regmhsnobp', 'mhsnobp');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Sem::class, 'regsem', 'semid');
    }

    /**
     * Scope: hanya registrasi yang benar-benar lunas (bukan AUTO dari KRS)
     */
    public function scopeLunas($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('regnobukti')
              ->orWhere('regnobukti', 'NOT LIKE', 'AUTO-%');
        })->where('regjumlahbayar', '>', 0);
    }

    /**
     * Cek apakah registrasi ini adalah auto-generated (belum lunas sungguhan)
     */
    public function getIsAutoAttribute(): bool
    {
        return str_starts_with($this->regnobukti ?? '', 'AUTO-')
            || ($this->regjumlahbayar ?? 0) == 0;
    }
}
