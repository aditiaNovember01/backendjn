<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabel tahunkur: tahun kurikulum yang valid (FK dari mahasiswa.mhstahunkur)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahunkur', function (Blueprint $table) {
            $table->integer('tahun');
            $table->primary('tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahunkur');
    }
};
