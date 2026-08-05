<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel konfigurasi tanpa FK:
 * setting, settingbiaya, biayakompre, periodebayar, label, mtklab
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── setting — konfigurasi global (biasanya 1 baris) ──────────────
        Schema::create('setting', function (Blueprint $table) {
            $table->integer('semaktif')->nullable();
            $table->dateTime('semkrsmulai')->nullable();
            $table->dateTime('semkrsselesai')->nullable();
            $table->dateTime('semubahkrsmulai')->nullable();
            $table->dateTime('semubahkrsselesai')->nullable();
            $table->dateTime('semnilaimulai')->nullable();
            $table->dateTime('semnilaiselesai')->nullable();
            $table->integer('portalisaktif')->nullable();    // int(1)
        });

        // ── settingbiaya — biaya kuliah per prodi/angkatan/kelas ─────────
        Schema::create('settingbiaya', function (Blueprint $table) {
            $table->integer('id', true);                     // int AUTO_INCREMENT
            $table->integer('prodi')->nullable();
            $table->integer('angkatan')->nullable();
            $table->char('kelas', 1)->nullable();            // R / E / K
            $table->double('biaya')->nullable();             // biaya penuh
            $table->double('biaya1')->nullable();            // cicilan 1
            $table->double('biaya2')->nullable();            // cicilan 2
            $table->double('pembangunan1')->nullable();
            $table->double('pembangunan2')->nullable();
            $table->double('pembangunan3')->nullable();
            $table->double('pembangunan4')->nullable();
            $table->double('orientasi')->nullable();

            $table->primary('id');
        });

        // ── biayakompre — biaya komprehensif/sidang ───────────────────────
        Schema::create('biayakompre', function (Blueprint $table) {
            $table->integer('id', true);                     // int AUTO_INCREMENT
            $table->string('periode', 30)->nullable();
            $table->double('biaya1')->nullable();
            $table->double('biaya2')->nullable();
            $table->double('biaya3')->nullable();
            $table->double('biaya4')->nullable();
            $table->integer('aktif')->nullable();            // int(1)

            $table->primary('id');
        });

        // ── periodebayar — periode buka/tutup pembayaran per semester ─────
        Schema::create('periodebayar', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('persemid')->nullable();         // int(5)
            $table->char('perstt', 1)->nullable();

            $table->primary('id');
        });

        // ── label — label kelas (A, B, C, -) — tabel kecil tanpa PK ──────
        Schema::create('label', function (Blueprint $table) {
            $table->string('labelnama', 2)->nullable();
        });

        // ── mtklab — mapping mata kuliah teori ↔ praktikum ───────────────
        Schema::create('mtklab', function (Blueprint $table) {
            $table->integer('mtklabid', true);               // int AUTO_INCREMENT
            $table->char('mtklabteori', 10)->nullable();
            $table->char('mtklabprak', 10)->nullable();

            $table->primary('mtklabid');
            $table->index('mtklabteori', 'imtklabteori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mtklab');
        Schema::dropIfExists('label');
        Schema::dropIfExists('periodebayar');
        Schema::dropIfExists('biayakompre');
        Schema::dropIfExists('settingbiaya');
        Schema::dropIfExists('setting');
    }
};
