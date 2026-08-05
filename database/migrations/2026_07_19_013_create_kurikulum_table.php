<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurikulum', function (Blueprint $table) {
            $table->bigInteger('kurid', true);              // bigint AUTO_INCREMENT
            $table->char('kurmtkid', 10)->nullable();
            $table->integer('kurprodiid')->nullable();
            $table->integer('kurtahun')->nullable();
            $table->integer('kursem')->default(0);          // int(2)
            $table->char('kurmtkidprasyarat', 10)->nullable();
            $table->string('kurmtkidprasyarat2', 10)->nullable();
            $table->string('kurmtkidprasyarat3', 10)->nullable();

            $table->primary('kurid');
            // UNIQUE KEY kurmtkid (kurmtkid, kurprodiid, kurtahun)
            $table->unique(['kurmtkid', 'kurprodiid', 'kurtahun'], 'kurmtkid');
            $table->index('kurmtkid',          'fkkur1');
            $table->index('kurprodiid',        'fkkur2');
            $table->index('kurmtkidprasyarat');

            // CONSTRAINT fkkur1 FOREIGN KEY (kurmtkid) REFERENCES matakuliah (mtkid) ON UPDATE CASCADE
            $table->foreign('kurmtkid',           'fkkur1')->references('mtkid')->on('matakuliah')->onUpdate('cascade');
            // CONSTRAINT fkkur2 FOREIGN KEY (kurprodiid) REFERENCES prodi (prodiid) ON UPDATE CASCADE
            $table->foreign('kurprodiid',         'fkkur2')->references('prodiid')->on('prodi')->onUpdate('cascade');
            // CONSTRAINT kurikulum_ibfk_1 FOREIGN KEY (kurmtkidprasyarat) REFERENCES matakuliah (mtkid) ON UPDATE CASCADE
            $table->foreign('kurmtkidprasyarat', 'kurikulum_ibfk_1')->references('mtkid')->on('matakuliah')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurikulum');
    }
};
