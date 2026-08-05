<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    protected $table      = 'agama';
    protected $primaryKey = 'agamaid';
    public    $timestamps = false;

    protected $fillable = ['agamaid', 'agamanama'];
}
