<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelasjadwal', function (Blueprint $table) {
            $table->bigInteger('jadwalid', true);            // bigint AUTO_INCREMENT
            $table->bigInteger('jadwalkelasid')->nullable(); // FK → kelas.kelasid
            $table->integer('jadwalruangid')->nullable();    // FK → ruang.ruid (no FK constraint di source)
            $table->integer('jadwaljamidawal')->nullable();  // int(1)
            $table->char('jadwalhari', 10)->nullable();
            $table->char('jadwaldosenid', 10)->nullable();   // FK → dosen.dosenid
            $table->timestamp('jadwaltanggalinput')->nullable();
            $table->char('jadwaluserinput', 20)->nullable();
            $table->integer('jadwaljamidakhir')->nullable(); // int(1)
            $table->integer('jadwalqsuts')->nullable();
            $table->integer('jadwalrespondenuts')->nullable();
            $table->integer('jadwalqsuas')->nullable();
            $table->integer('jadwalrespondenuas')->nullable();

            $table->primary('jadwalid');
            $table->index('jadwaldosenid',  'fkjadwal2');
            $table->index('jadwalhari',     'ikj1');
            $table->index('jadwaljamidawal','ikj2');
            $table->index('jadwaljamidakhir','ikj3');
            $table->index('jadwalkelasid',  'ikj4');

            // CONSTRAINT fkjadwal1 FOREIGN KEY (jadwalkelasid) REFERENCES kelas (kelasid) ON DELETE CASCADE ON UPDATE CASCADE
            $table->foreign('jadwalkelasid', 'fkjadwal1')
                  ->references('kelasid')->on('kelas')
                  ->onDelete('cascade')->onUpdate('cascade');

            // CONSTRAINT fkjadwal2 FOREIGN KEY (jadwaldosenid) REFERENCES dosen (dosenid) ON UPDATE CASCADE
            $table->foreign('jadwaldosenid', 'fkjadwal2')
                  ->references('dosenid')->on('dosen')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelasjadwal');
    }
};
