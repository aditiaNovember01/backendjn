<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kurikulum extends Model
{
    protected $table    = 'kurikulum';
    protected $primaryKey = 'kurid';
    public    $timestamps = false;

    protected $fillable = [
        'kurid', 'kurmtkid', 'kurprodiid', 'kurtahun', 'kursem',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'kurmtkid', 'mtkid');
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'kurprodiid', 'prodiid');
    }
}
