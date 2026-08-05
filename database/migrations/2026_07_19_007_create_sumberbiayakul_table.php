<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sumberbiayakul', function (Blueprint $table) {
            $table->integer('sumberid', true);   // AUTO_INCREMENT
            $table->string('sumbernama', 50)->nullable();
            $table->primary('sumberid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sumberbiayakul');
    }
};
