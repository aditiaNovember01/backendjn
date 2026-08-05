<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table      = 'dosen';
    protected $primaryKey = 'dosenid';
    protected $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'dosenid', 'dosennama', 'dosengelardepan', 'dosengelarbelakang',
        'dosentelp', 'dosennidn', 'dosenprodiid', 'dosenstatus',
    ];

    /**
     * Nama dosen lengkap dengan gelar.
     */
    public function getNamaLengkapAttribute(): string
    {
        $depan   = $this->dosengelardepan ? $this->dosengelardepan . ' ' : '';
        $belakang = $this->dosengelarbelakang ? ', ' . $this->dosengelarbelakang : '';
        return $depan . $this->dosennama . $belakang;
    }
}
