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
}
