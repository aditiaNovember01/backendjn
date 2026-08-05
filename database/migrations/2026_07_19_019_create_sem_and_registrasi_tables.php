<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * spp + registrasi — keduanya FK ke mahasiswa & sem
 * registrasi harus ada sebelum krs (krs FK ke registrasi)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── spp ──────────────────────────────────────────────────────────
        Schema::create('spp', function (Blueprint $table) {
            $table->bigInteger('sppid', true);              // bigint AUTO_INCREMENT
            $table->char('sppmhsnobp', 7)->nullable();
            $table->integer('sppsem')->nullable();           // int(5)
            $table->double('spptagihan')->nullable();

            $table->primary('sppid');
            $table->index('sppmhsnobp');
            $table->index('sppsem');

            // CONSTRAINT spp_ibfk_1 FOREIGN KEY (sppmhsnobp) REFERENCES mahasiswa (mhsnobp) ON UPDATE CASCADE
            $table->foreign('sppmhsnobp', 'spp_ibfk_1')->references('mhsnobp')->on('mahasiswa')->onUpdate('cascade');
            // CONSTRAINT spp_ibfk_2 FOREIGN KEY (sppsem) REFERENCES sem (semid) ON UPDATE CASCADE
            $table->foreign('sppsem',     'spp_ibfk_2')->references('semid')  ->on('sem')      ->onUpdate('cascade');
        });

        // ── registrasi ───────────────────────────────────────────────────
        Schema::create('registrasi', function (Blueprint $table) {
            $table->bigInteger('regid', true);              // bigint AUTO_INCREMENT
            $table->char('regmhsnobp', 7)->nullable();
            $table->integer('regsem')->nullable();
            $table->integer('regjumlahbayar')->nullable();
            $table->date('regtanggalbayar')->nullable();
            $table->char('reguserinput', 20)->nullable();
            // timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            $table->timestamp('regtanggalinsert')->useCurrent()->useCurrentOnUpdate();
            $table->string('regnobukti', 50)->nullable();

            $table->primary('regid');
            // UNIQUE KEY regmhsnobp (regmhsnobp, regsem)
            $table->unique(['regmhsnobp', 'regsem'], 'regmhsnobp');
            $table->index('regmhsnobp', 'fkreg1');
            $table->index('regsem',     'registrasi_ibfk_1');

            // CONSTRAINT fkreg1 FOREIGN KEY (regmhsnobp) REFERENCES mahasiswa (mhsnobp) ON UPDATE CASCADE
            $table->foreign('regmhsnobp', 'fkreg1')          ->references('mhsnobp')->on('mahasiswa')->onUpdate('cascade');
            // CONSTRAINT registrasi_ibfk_1 FOREIGN KEY (regsem) REFERENCES sem (semid) ON UPDATE CASCADE
            $table->foreign('regsem',     'registrasi_ibfk_1')->references('semid')  ->on('sem')      ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrasi');
        Schema::dropIfExists('spp');
    }
};
