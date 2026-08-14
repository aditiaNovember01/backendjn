<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin default panel Filament
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Administrator',
                'email'    => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'mhsnobp'  => null,
            ]
        );

        $this->command->info('Admin panel : admin@gmail.com | password: admin123');

        // Semua data kampus + mahasiswa testing
        $this->call(Mahasiswa2210050Seeder::class);
    }
}
