<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('krs', function (Blueprint $table) {
            $table->bigInteger('krsid', true);              // bigint AUTO_INCREMENT
            $table->bigInteger('krsregid');                 // bigint NOT NULL, FK → registrasi
            $table->char('krsmhsnobp', 7)->nullable();      // FK → mahasiswa
            $table->bigInteger('krskelasid')->nullable();   // FK → kelas
            $table->char('krsnilai', 1)->default('');       // A/B/C/D/E
            $table->integer('krssem')->nullable();
            $table->integer('krsjmlabsen')->default(0);
            $table->dateTime('krstanggalambil')->nullable();
            $table->char('krsuserambil', 20)->nullable();
            $table->dateTime('krstanggalhapus')->nullable();
            $table->char('krsuserhapus', 20)->nullable();
            $table->integer('krshapus')->default(0);        // int(1): 0=aktif, 1=dihapus
            $table->dateTime('krstanggalnilai')->nullable();
            $table->char('krsusernilai', 20)->nullable();
            $table->integer('krsbobot')->default(0);        // int(1): 0-4
            $table->string('krsinputnilaimetode', 10)->default('portal');
            $table->integer('krsapproved')->default(0);     // int(1): 0=pending, 1=approved
            $table->dateTime('krstglapproved')->nullable();
            $table->integer('krskomplain')->default(0);     // int(1)

            $table->primary('krsid');
            $table->index('krsmhsnobp', 'fkkrs1');
            $table->index('krskelasid', 'fkkrs2');
            $table->index('krsregid');
            $table->index('krssem',     'ikrs1');

            // CONSTRAINT fkkrs1 FOREIGN KEY (krsmhsnobp) REFERENCES mahasiswa (mhsnobp) ON UPDATE CASCADE
            $table->foreign('krsmhsnobp', 'fkkrs1')    ->references('mhsnobp')->on('mahasiswa') ->onUpdate('cascade');
            // CONSTRAINT fkkrs2 FOREIGN KEY (krskelasid) REFERENCES kelas (kelasid) ON UPDATE CASCADE
            $table->foreign('krskelasid', 'fkkrs2')    ->references('kelasid')->on('kelas')     ->onUpdate('cascade');
            // CONSTRAINT krs_ibfk_1 FOREIGN KEY (krsregid) REFERENCES registrasi (regid) ON UPDATE CASCADE
            $table->foreign('krsregid',   'krs_ibfk_1')->references('regid')  ->on('registrasi')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};
