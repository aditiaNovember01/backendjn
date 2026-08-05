<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel bukti_pembayaran — menyimpan upload bukti bayar dari mahasiswa
 * dan tracking status konfirmasi oleh admin.
 * Tidak ada di schema DB kampus original.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_pembayaran', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('mhsnobp', 7);                         // FK → mahasiswa.mhsnobp
            $table->integer('sppsem');                          // FK → sem.semid (int 5 di source)
            $table->double('jumlah_bayar');
            $table->string('file_path', 255)->nullable();       // path file original
            $table->string('file_compressed', 255)->nullable(); // path file hasil kompresi GD
            $table->enum('status', ['pending', 'dikonfirmasi', 'ditolak'])->default('pending');
            $table->string('catatan', 200)->nullable();         // alasan penolakan
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index('mhsnobp');
            $table->index('sppsem');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_pembayaran');
    }
};
