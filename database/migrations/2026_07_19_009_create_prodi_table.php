<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prodi', function (Blueprint $table) {
            $table->integer('prodiid');
            $table->char('prodinama', 50)->nullable();
            $table->string('prodinamaasing', 50)->nullable();
            $table->integer('prodifakid')->nullable();
            $table->date('proditanggalsk')->nullable();
            $table->char('prodinosk', 40)->nullable();
            $table->integer('prodijpid')->nullable();
            $table->char('prodipejabat', 50)->nullable();
            $table->string('prodipejabat2', 30)->nullable();
            $table->string('prodipejabat3', 30)->nullable();
            $table->char('prodikodeps', 6)->nullable();
            $table->char('prodikodejenjang', 1)->nullable();
            $table->integer('prodiptid')->default(1);
            $table->integer('prodinbkode')->default(1);

            $table->primary('prodiid');
            $table->index('prodifakid', 'fkprodi1');
            $table->index('prodiptid', 'xprodi2');
            $table->index('prodinbkode', 'xprodi3');

            // CONSTRAINT fkprodi1 FOREIGN KEY (prodifakid) REFERENCES fakultas (fakid)
            $table->foreign('prodifakid', 'fkprodi1')->references('fakid')->on('fakultas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prodi');
    }
};
