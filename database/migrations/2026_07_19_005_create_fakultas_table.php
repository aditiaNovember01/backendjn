<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fakultas', function (Blueprint $table) {
            $table->integer('fakid');
            $table->char('faknama', 50)->nullable();
            $table->string('fakpim', 50)->nullable();
            $table->string('fakwapim', 50)->nullable();
            $table->primary('fakid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fakultas');
    }
};
