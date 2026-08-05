<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Krs extends Model
{
    protected $table      = 'krs';
    protected $primaryKey = 'krsid';
    public    $timestamps = false;

    protected $fillable = [
        'krsid', 'krsregid', 'krsmhsnobp', 'krskelasid',
        'krsnilai', 'krssem', 'krsjmlabsen',
        'krstanggalambil', 'krsuserambil',
        'krstanggalhapus', 'krsuserhapus',
        'krshapus', 'krstanggalnilai', 'krsusernilai',
        'krsbobot', 'krsapproved', 'krstglapproved', 'krskomplain',
    ];

    protected $casts = [
        'krshapus'      => 'boolean',
        'krsapproved'   => 'boolean',
        'krsbobot'      => 'integer',
        'krsjmlabsen'   => 'integer',
        'krstanggalambil' => 'datetime',
        'krstglapproved'  => 'datetime',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'krsmhsnobp', 'mhsnobp');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'krskelasid', 'kelasid');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Sem::class, 'krssem', 'semid');
    }

    public function registrasi(): BelongsTo
    {
        return $this->belongsTo(Registrasi::class, 'krsregid', 'regid');
    }

    // ── Scope: hanya KRS yang tidak dihapus ───────────────────
    public function scopeAktif($query)
    {
        return $query->where('krshapus', 0);
    }

    // ── Accessor: status teks ─────────────────────────────────
    public function getStatusKrsAttribute(): string
    {
        if ($this->krshapus) {
            return 'Dibatalkan';
        }
        return $this->krsapproved ? 'Approved' : 'Pending';
    }

    // ── Accessor: keterangan nilai ────────────────────────────
    public function getKetNilaiAttribute(): string
    {
        if (! $this->krsnilai) return '-';
        return in_array($this->krsnilai, ['D', 'E']) ? 'Gagal' : 'Lulus';
    }
}
