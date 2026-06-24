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
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
            ]
        );

         // ===============================
        // AKUN LOGIN 2
        // ===============================
        User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'staff',
                'email_verified_at' => now(),
                'password' => Hash::make('staff123'),
            ]
        );

        // ===============================
        // AKUN LOGIN 3
        // ===============================
        User::firstOrCreate(
            ['email' => 'kepala@gmail.com'],
            [
                'name' => 'kepala',
                'email_verified_at' => now(),
                'password' => Hash::make('kepala123'),
            ]
        );

         // ===============================
        // AKUN LOGIN 4
        // ===============================
        User::firstOrCreate(
            ['email' => 'adli@gmail.com'],
            [
                'name' => 'adli',
                'email_verified_at' => now(),
                'password' => Hash::make('nia123'),
            ]
        );
    }
}