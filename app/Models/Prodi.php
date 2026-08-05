<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    protected $table      = 'prodi';
    protected $primaryKey = 'prodiid';
    public    $timestamps = false;

    protected $fillable = ['prodiid', 'prodinama', 'prodinamaasing', 'prodifakid'];
}
