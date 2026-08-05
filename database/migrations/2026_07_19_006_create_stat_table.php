<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabel stat: status mahasiswa (Aktif, Non-Aktif, Cuti, dll)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stat', function (Blueprint $table) {
            $table->integer('statid');
            $table->char('statnama', 30)->nullable();
            $table->primary('statid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stat');
    }
};
