<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->char('dosenid', 10);
            $table->char('dosennama', 100)->nullable();
            $table->char('dosenalamat', 50)->nullable();
            $table->char('dosentelp', 20)->nullable();
            $table->char('dosengelardepan', 15)->nullable();
            $table->char('dosengelarbelakang', 15)->nullable();
            $table->integer('dosenjpid')->nullable();
            $table->integer('dosenprodiid')->nullable();
            $table->char('dosennidn', 20)->nullable();
            $table->integer('dosenstatus')->nullable();   // int(1) di source

            $table->primary('dosenid');
            $table->index('dosenprodiid', 'fkdosen1');
            $table->index('dosennama', 'idosen1');

            // CONSTRAINT fkdosen1 FOREIGN KEY (dosenprodiid) REFERENCES prodi (prodiid) ON UPDATE CASCADE
            $table->foreign('dosenprodiid', 'fkdosen1')->references('prodiid')->on('prodi')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
