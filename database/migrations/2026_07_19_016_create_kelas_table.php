<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->bigInteger('kelasid', true);             // bigint AUTO_INCREMENT
            $table->char('kelaskode', 11)->nullable();       // format 20252-1-001
            $table->bigInteger('kelaskurid')->nullable();    // bigint FK → kurikulum
            $table->integer('kelasprodiid')->nullable();     // int(1) di source, integer di Laravel
            $table->integer('kelassem')->nullable();         // FK → sem.semid
            $table->integer('kelasmax')->default(30);
            $table->timestamp('kelastanggalinput')->nullable();
            $table->char('kelasuserinput', 20)->nullable();
            $table->dateTime('kelastanggalubah')->nullable();
            $table->char('kelasuserubah', 20)->nullable();
            $table->string('kelasnobpmin', 7)->default('0');
            $table->string('kelasnobpmax', 7)->default('9999999');
            $table->char('kelaskel', 1)->default('R');
            $table->string('kelaslabel', 2)->default('-');   // label kelas: A, B, C
            $table->integer('kelasnilai')->nullable();
            $table->string('kelasket', 200)->nullable();

            $table->primary('kelasid');
            // UNIQUE KEY kelaskode (kelaskode)
            $table->unique('kelaskode');
            $table->index('kelaskurid',    'fkkelas1');
            $table->index('kelaskode',     'ikelas1');
            $table->index('kelasprodiid');
            $table->index('kelassem',      'kelas_ibfk_1');

            // CONSTRAINT fkkelas1 FOREIGN KEY (kelaskurid) REFERENCES kurikulum (kurid) ON UPDATE CASCADE
            $table->foreign('kelaskurid',   'fkkelas1')   ->references('kurid')   ->on('kurikulum')->onUpdate('cascade');
            // CONSTRAINT kelas_ibfk_1 FOREIGN KEY (kelassem) REFERENCES sem (semid) ON UPDATE CASCADE
            $table->foreign('kelassem',     'kelas_ibfk_1')->references('semid')   ->on('sem')      ->onUpdate('cascade');
            // CONSTRAINT kelas_ibfk_2 FOREIGN KEY (kelasprodiid) REFERENCES prodi (prodiid) ON UPDATE CASCADE
            $table->foreign('kelasprodiid', 'kelas_ibfk_2')->references('prodiid')->on('prodi')    ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
