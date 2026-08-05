<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabel kel: kelas biaya mahasiswa (R=Reguler, E=Reguler Malam, K=KIP)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kel', function (Blueprint $table) {
            $table->char('kelid', 1);
            $table->string('kelnama', 20)->nullable();
            $table->primary('kelid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kel');
    }
};
