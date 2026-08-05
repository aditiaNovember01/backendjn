<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabel kelompok: kelompok mata kuliah (MKB, MKK, MKP, dll)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelompok', function (Blueprint $table) {
            $table->char('kelid', 10);
            $table->char('kelnama', 50)->nullable();
            $table->primary('kelid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelompok');
    }
};
