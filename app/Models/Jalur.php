<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jalur extends Model
{
    protected $table      = 'jalur';
    protected $primaryKey = 'jalurid';
    public    $timestamps = false;

    protected $fillable = ['jalurid', 'jalurnama'];
}
