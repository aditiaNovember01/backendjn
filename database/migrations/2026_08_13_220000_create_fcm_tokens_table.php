<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel sudah ada dari run pertama (gagal di unique index karena token=TEXT)
        // Alter: ubah token TEXT → VARCHAR(255) lalu tambah unique index
        if (Schema::hasTable('fcm_tokens')) {
            Schema::table('fcm_tokens', function (Blueprint $table) {
                // Ubah TEXT → VARCHAR(255) agar bisa di-index
                $table->string('token', 255)->change();
            });

            Schema::table('fcm_tokens', function (Blueprint $table) {
                // Drop index lama jika ada, abaikan jika tidak ada
                try {
                    $table->dropUnique('fcm_tokens_nobp_token_unique');
                } catch (\Throwable $e) {
                    // Belum ada, lanjut
                }
                $table->unique(['nobp', 'token'], 'fcm_tokens_nobp_token_unique');
            });

            return;
        }

        // Instalasi baru — buat tabel fresh
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('nobp', 10)->index();
            $table->string('token', 255);
            $table->string('platform', 10)->default('android');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['nobp', 'token'], 'fcm_tokens_nobp_token_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
