<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelasJadwal extends Model
{
    protected $table      = 'kelasjadwal';
    protected $primaryKey = 'jadwalid';
    public    $timestamps = false;

    protected $fillable = [
        'jadwalid', 'jadwalkelasid', 'jadwalruangid',
        'jadwaldosenid', 'jadwalhari',
        'jadwaljamidawal', 'jadwaljamidakhir',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'jadwalkelasid', 'kelasid');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'jadwaldosenid', 'dosenid');
    }

    public function ruang(): BelongsTo
    {
        return $this->belongsTo(Ruang::class, 'jadwalruangid', 'ruid');
    }
}
