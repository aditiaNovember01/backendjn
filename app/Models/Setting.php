<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table    = 'setting';
    public    $timestamps = false;
    protected $primaryKey = null;
    public    $incrementing = false;

    protected $fillable = [
        'semaktif', 'semkrsmulai', 'semkrsselesai',
        'semubahkrsmulai', 'semubahkrsselesai',
        'semnilaimulai', 'semnilaiselesai',
        'portalisaktif',
    ];

    public static function semesterAktif(): int
    {
        return (int) self::value('semaktif');
    }
}
