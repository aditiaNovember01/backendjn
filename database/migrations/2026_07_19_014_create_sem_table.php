<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sem', function (Blueprint $table) {
            // semid int(5) NOT NULL DEFAULT 0  — format YYYYS: 20251, 20252
            $table->integer('semid')->default(0);
            $table->string('semnama', 18)->nullable();       // "Ganjil 2025/2026"
            $table->dateTime('semmulai')->nullable();
            $table->dateTime('semselesai')->nullable();
            $table->dateTime('semtglkrsmulai')->nullable();
            $table->dateTime('semtglkrsselesai')->nullable();
            $table->dateTime('semtglnilaimulai')->nullable();
            $table->dateTime('semtglnilaiselesai')->nullable();
            $table->integer('semsksbaru')->nullable();       // int(2)
            $table->integer('semsksbss')->nullable();        // int(2)
            $table->integer('semangkatanbaru')->nullable();  // int(4)
            $table->integer('semlalu')->nullable();          // int(5) — semid semester lalu
            $table->integer('semaktif')->default(0);         // int(1): 1=aktif
            $table->date('semtglkomplain')->nullable();

            $table->primary('semid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sem');
    }
};
