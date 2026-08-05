<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    protected $table      = 'matakuliah';
    protected $primaryKey = 'mtkid';
    protected $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'mtkid', 'mtknama', 'mtkasing', 'mtksks', 'mtkdesc', 'mtkkelid',
    ];

    protected $casts = [
        'mtksks' => 'integer',
    ];

    public function kurikulumList(): HasMany
    {
        return $this->hasMany(Kurikulum::class, 'kurmtkid', 'mtkid');
    }
}
