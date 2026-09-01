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
            $table->string('nobp', 10)->index();
            $table->string('token', 255);              // varchar(255) — bisa di-unique
            $table->string('platform', 10)->default('android');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Unique per nobp+token (varchar 255 = bisa di-index langsung)
            $table->unique(['nobp', 'token'], 'fcm_tokens_nobp_token_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
