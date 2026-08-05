<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelasAngkatan extends Model
{
    protected $table    = 'kelasangkatan';
    public    $timestamps = false;
    protected $primaryKey = null;
    public    $incrementing = false;

    protected $fillable = ['kelasangkelasid', 'kelasangangkatan'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelasangkelasid', 'kelasid');
    }
}
