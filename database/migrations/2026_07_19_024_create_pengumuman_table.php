<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel pengumuman — dibuat khusus untuk aplikasi mobile Halo Jayanusa.
 * Tidak ada di schema DB kampus original.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('judul', 200);
            $table->text('isi');
            $table->date('tgl_publish')->nullable();
            $table->boolean('aktif')->default(true);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
