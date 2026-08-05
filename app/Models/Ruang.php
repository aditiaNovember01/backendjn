<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruang extends Model
{
    protected $table      = 'ruang';
    protected $primaryKey = 'ruid';
    public    $timestamps = false;

    protected $fillable = ['ruid', 'runama'];
}
