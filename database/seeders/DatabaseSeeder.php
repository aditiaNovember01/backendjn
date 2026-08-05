<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin default
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

        // Pastikan admin yang sudah ada rolenya benar
        User::where('email', 'admin@gmail.com')->update(['role' => 'admin']);

        $this->command->info('Admin: admin@gmail.com | password: admin123');

        // Seed data kampus
        $this->call(KampusSeeder::class);
    }
}
