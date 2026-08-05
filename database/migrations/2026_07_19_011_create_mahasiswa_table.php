<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            // Primary key: mhsnobp char(7)
            $table->char('mhsnobp', 7);
            $table->char('mhsnama', 50);                        // NOT NULL
            $table->char('mhsalamat', 200)->nullable();
            $table->integer('mhsangkatan');                     // NOT NULL
            $table->integer('mhsprodiid');                      // NOT NULL
            $table->integer('mhsagamaid')->nullable();
            $table->integer('mhsjalurid')->nullable();
            $table->integer('mhsstatid');                       // NOT NULL
            $table->string('mhstgllhr', 20)->nullable();        // varchar(20) — format lama
            $table->char('mhstmplhr', 50)->nullable();
            $table->char('mhsjkl', 1)->nullable();              // L / P
            $table->char('mhsortu', 50)->nullable();            // nama ayah
            $table->string('mhsibu', 50)->nullable();           // varchar(50)
            $table->char('mhstelp', 50)->nullable();
            $table->integer('mhstahunkur')->nullable();         // int(4)
            $table->char('mhskel', 1)->default('R');
            $table->string('mhsasalsekolah', 100)->nullable();
            $table->integer('mhssemidmasuk')->nullable();       // int(5) format YYYYS
            $table->string('mhsnik', 50)->nullable();
            $table->char('mhsnisn', 10)->default('0');
            $table->integer('mhsumberbiayaid')->nullable();
            $table->string('mhsemail', 100)->nullable()->default('');
            $table->date('mhstgllahir')->nullable();            // date — format standar
            $table->string('mhskelurahan', 100)->nullable();
            $table->string('mhskecamatan', 100)->nullable();

            $table->primary('mhsnobp');
            $table->index('mhsprodiid',      'fkmhs1');
            $table->index('mhsagamaid',      'fkmhs2');
            $table->index('mhsstatid',       'fkmhs3');
            $table->index('mhsjalurid',      'fkmhs4');
            $table->index('mhsnama',         'imhs1');
            $table->index('mhsangkatan',     'imhs2');
            $table->index('mhstahunkur');
            $table->index('mhskel');
            $table->index('mhsumberbiayaid');

            // CONSTRAINT fkmhs1 FOREIGN KEY (mhsprodiid) REFERENCES prodi (prodiid) ON UPDATE CASCADE
            $table->foreign('mhsprodiid',      'fkmhs1')->references('prodiid')->on('prodi')->onUpdate('cascade');
            // CONSTRAINT fkmhs2 FOREIGN KEY (mhsagamaid) REFERENCES agama (agamaid) ON UPDATE CASCADE
            $table->foreign('mhsagamaid',      'fkmhs2')->references('agamaid')->on('agama')->onUpdate('cascade');
            // CONSTRAINT fkmhs3 FOREIGN KEY (mhsstatid) REFERENCES stat (statid) ON UPDATE CASCADE
            $table->foreign('mhsstatid',       'fkmhs3')->references('statid')->on('stat')->onUpdate('cascade');
            // CONSTRAINT fkmhs4 FOREIGN KEY (mhsjalurid) REFERENCES jalur (jalurid) ON UPDATE CASCADE
            $table->foreign('mhsjalurid',      'fkmhs4')->references('jalurid')->on('jalur')->onUpdate('cascade');
            // CONSTRAINT mahasiswa_ibfk_1 FOREIGN KEY (mhstahunkur) REFERENCES tahunkur (tahun) ON UPDATE CASCADE
            $table->foreign('mhstahunkur', 'mahasiswa_ibfk_1')->references('tahun')->on('tahunkur')->onUpdate('cascade');
            // CONSTRAINT mahasiswa_ibfk_2 FOREIGN KEY (mhskel) REFERENCES kel (kelid) ON UPDATE CASCADE
            $table->foreign('mhskel',      'mahasiswa_ibfk_2')->references('kelid')->on('kel')->onUpdate('cascade');
            // CONSTRAINT mahasiswa_ibfk_3 FOREIGN KEY (mhsumberbiayaid) REFERENCES sumberbiayakul (sumberid) ON UPDATE CASCADE
            $table->foreign('mhsumberbiayaid', 'mahasiswa_ibfk_3')->references('sumberid')->on('sumberbiayakul')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
