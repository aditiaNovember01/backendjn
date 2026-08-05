<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jalur', function (Blueprint $table) {
            $table->integer('jalurid');
            $table->char('jalurnama', 30)->nullable();
            $table->primary('jalurid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jalur');
    }
};
