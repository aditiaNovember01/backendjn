<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * pembayaran_kompre + pembayaran_wisuda
 * Keduanya FK ke mahasiswa
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── pembayaran_kompre ─────────────────────────────────────────────
        Schema::create('pembayaran_kompre', function (Blueprint $table) {
            $table->bigInteger('pembkompreid', true);           // bigint AUTO_INCREMENT
            $table->char('pembkompremhsnobp', 7)->nullable();
            $table->date('pembkompretgl')->nullable();
            $table->string('pembkomprenobukti', 100)->nullable();
            $table->double('pembkomprejumlah')->nullable();
            $table->string('pembkompreuserinput', 50)->nullable();
            $table->integer('pembkompreke')->nullable();
            $table->string('pembkompreperiode', 30)->nullable();

            $table->primary('pembkompreid');
            $table->index('pembkompremhsnobp');

            // CONSTRAINT pembayaran_kompre_ibfk_1 FOREIGN KEY (pembkompremhsnobp) REFERENCES mahasiswa (mhsnobp) ON UPDATE CASCADE
            $table->foreign('pembkompremhsnobp', 'pembayaran_kompre_ibfk_1')
                  ->references('mhsnobp')->on('mahasiswa')->onUpdate('cascade');
        });

        // ── pembayaran_wisuda ─────────────────────────────────────────────
        Schema::create('pembayaran_wisuda', function (Blueprint $table) {
            $table->bigInteger('wispembid', true);              // bigint AUTO_INCREMENT
            $table->char('wispembmhsnobp', 7)->nullable();
            $table->dateTime('wispembtgl')->nullable();
            $table->string('wispembnobukti', 50)->nullable();
            $table->double('wispembjumlah')->nullable();
            $table->string('wispembuserinput', 50)->nullable();

            $table->primary('wispembid');
            $table->index('wispembmhsnobp');

            // CONSTRAINT pembayaran_wisuda_ibfk_1 FOREIGN KEY (wispembmhsnobp) REFERENCES mahasiswa (mhsnobp) ON UPDATE CASCADE
            $table->foreign('wispembmhsnobp', 'pembayaran_wisuda_ibfk_1')
                  ->references('mhsnobp')->on('mahasiswa')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_wisuda');
        Schema::dropIfExists('pembayaran_kompre');
    }
};
