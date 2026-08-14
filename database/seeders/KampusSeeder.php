<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * KampusSeeder — delegasi ke Mahasiswa2210050Seeder.
 * Semua data kampus, MK, jadwal, SPP, KRS ada di seeder tersebut.
 */
class KampusSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(Mahasiswa2210050Seeder::class);
    }
}
