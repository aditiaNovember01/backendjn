<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agama', function (Blueprint $table) {
            $table->integer('agamaid');
            $table->char('agamanama', 30)->nullable();
            $table->primary('agamaid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agama');
    }
};
