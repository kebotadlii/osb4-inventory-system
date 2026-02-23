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
        User::firstOrCreate(
            ['email' => 'adliosb4@gmail.com'],
            [
                'name' => 'Adli',
                'email_verified_at' => now(),
                'password' => Hash::make('adliosb4'),
            ]
        );

        // ===============================
        // AKUN LOGIN 2
        // ===============================
        User::firstOrCreate(
            ['email' => 'osb4@gmail.com'],
            [
                'name' => 'OSB4',
                'email_verified_at' => now(),
                'password' => Hash::make('osb4'),
            ]
        );
    }
}