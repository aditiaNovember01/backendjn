<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sem extends Model
{
    protected $table      = 'sem';
    protected $primaryKey = 'semid';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'semid', 'semnama', 'semmulai', 'semselesai',
        'semtglkrsmulai', 'semtglkrsselesai',
        'semtglnilaimulai', 'semtglnilaiselesai',
        'semsksbaru', 'semsksbss', 'semangkatanbaru',
        'semlalu', 'semaktif',
    ];

    protected $casts = [
        'semaktif'          => 'boolean',
        'semmulai'          => 'datetime',
        'semselesai'        => 'datetime',
        'semtglkrsmulai'    => 'datetime',
        'semtglkrsselesai'  => 'datetime',
    ];

    /**
     * Ambil semester yang sedang aktif (semaktif = 1).
     */
    public static function aktif(): ?self
    {
        return self::where('semaktif', 1)->first();
    }

    /**
     * Cek apakah periode KRS sedang terbuka.
     */
    public function isKrsOpen(): bool
    {
        $now = now();
        return $this->semtglkrsmulai && $this->semtglkrsselesai
            && $now->between($this->semtglkrsmulai, $this->semtglkrsselesai);
    }
}
