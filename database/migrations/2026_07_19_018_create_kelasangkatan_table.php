<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel kelasangkatan tidak punya primary key — hanya unique composite
        Schema::create('kelasangkatan', function (Blueprint $table) {
            $table->bigInteger('kelasangkelasid')->nullable(); // FK → kelas.kelasid
            $table->integer('kelasangangkatan')->nullable();   // int(4) — tahun angkatan

            // UNIQUE KEY kelasangkelasid (kelasangkelasid, kelasangangkatan)
            $table->unique(['kelasangkelasid', 'kelasangangkatan'], 'kelasangkelasid');

            // CONSTRAINT kelasangkatan_ibfk_1 FOREIGN KEY (kelasangkelasid) REFERENCES kelas (kelasid)
            // ON DELETE CASCADE ON UPDATE CASCADE
            $table->foreign('kelasangkelasid', 'kelasangkatan_ibfk_1')
                  ->references('kelasid')->on('kelas')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelasangkatan');
    }
};
