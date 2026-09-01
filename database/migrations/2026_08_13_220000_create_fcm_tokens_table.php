<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('nobp', 10)->index();           // NoBP mahasiswa atau dosenid
            $table->text('token');                          // FCM device token (panjang ~150+ char)
            $table->string('platform', 10)->default('android'); // android / ios
            $table->timestamp('last_used_at')->nullable();  // Terakhir aktif
            $table->timestamps();

            // Satu kombinasi nobp+token harus unik
            $table->unique(['nobp', 'token'], 'fcm_tokens_nobp_token_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
