<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruang', function (Blueprint $table) {
            $table->integer('ruid', true);          // int AUTO_INCREMENT
            $table->char('runama', 20)->nullable();

            $table->primary('ruid');
            $table->index('runama', 'iruang1');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruang');
    }
};
