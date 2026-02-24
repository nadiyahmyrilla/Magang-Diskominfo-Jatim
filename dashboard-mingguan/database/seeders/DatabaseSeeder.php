<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@dashboard.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123456'),
                'role' => 'admin',
                'last_login_at' => now(),
            ]
        );

        // Create Regular User
        User::firstOrCreate(
            ['email' => 'user@dashboard.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('user123456'),
                'role' => 'user',
                'last_login_at' => now(),
            ]
        );
    }
}
