<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan kolom mhsnobp + role ke tabel users (Laravel default).
 * - mhsnobp → link user ke mahasiswa (null untuk admin)
 * - role    → 'admin' | 'mahasiswa'
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->char('mhsnobp', 7)->nullable()->unique()->after('id');
            $table->enum('role', ['admin', 'mahasiswa'])->default('mahasiswa')->after('mhsnobp');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mhsnobp', 'role']);
        });
    }
};
