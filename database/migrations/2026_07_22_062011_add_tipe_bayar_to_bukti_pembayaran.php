<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom tipe_bayar ke tabel bukti_pembayaran.
 * Nilai: 'penuh' | 'cicilan1' | 'cicilan2'
 * Default 'penuh' untuk data lama yang sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bukti_pembayaran', function (Blueprint $table) {
            $table->enum('tipe_bayar', ['penuh', 'cicilan1', 'cicilan2'])
                  ->default('penuh')
                  ->after('jumlah_bayar')
                  ->comment('Tipe pembayaran: penuh, cicilan1, atau cicilan2 (pelunasan)');
        });
    }

    public function down(): void
    {
        Schema::table('bukti_pembayaran', function (Blueprint $table) {
            $table->dropColumn('tipe_bayar');
        });
    }
};
