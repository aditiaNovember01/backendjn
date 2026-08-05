<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan role 'dosen' ke enum users.role
 * dan kolom dosenid (FK → dosen.dosenid) agar
 * login dosen bisa pakai Sanctum token.
 */
return new class extends Migration
{
    public function up(): void
    {
        // MySQL: ubah enum agar termasuk 'dosen'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','mahasiswa','dosen') NOT NULL DEFAULT 'mahasiswa'");

        Schema::table('users', function (Blueprint $table) {
            // dosenid nullable karena mahasiswa & admin tidak punya
            $table->string('dosenid', 10)->nullable()->unique()->after('mhsnobp');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('dosenid');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','mahasiswa') NOT NULL DEFAULT 'mahasiswa'");
    }
};
