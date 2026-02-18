<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ===============================
        // AKUN LOGIN 1
        // ===============================
        User::create([
            'name' => 'Adli',
            'email' => 'adliosb4@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('adliosb4'),
        ]);

        // ===============================
        // AKUN LOGIN 2
        // ===============================
        User::create([
            'name' => 'User Dua',
            'email' => 'user2@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
        ]);
    }
}
