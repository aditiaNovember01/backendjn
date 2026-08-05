<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matakuliah', function (Blueprint $table) {
            $table->char('mtkid', 10);
            $table->char('mtknama', 100)->nullable();
            $table->char('mtkasing', 100)->nullable();
            $table->integer('mtksks')->nullable();
            $table->char('mtkdesc', 100)->nullable();
            $table->char('mtkkelid', 10)->nullable();
            $table->string('mtkuserinput', 30)->nullable();
            $table->dateTime('mtktglinput')->nullable();
            $table->string('mtkuserubah', 30)->nullable();
            $table->dateTime('mtktglubah')->nullable();

            $table->primary('mtkid');
            $table->index('mtkkelid');

            // CONSTRAINT matakuliah_ibfk_1 FOREIGN KEY (mtkkelid) REFERENCES kelompok (kelid) ON UPDATE CASCADE
            $table->foreign('mtkkelid', 'matakuliah_ibfk_1')
                  ->references('kelid')->on('kelompok')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matakuliah');
    }
};
