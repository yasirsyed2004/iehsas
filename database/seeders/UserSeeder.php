<?php
// File: database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'sunny@iehsas.com'],
            [
                'name' => 'Sunny',
                'email' => 'sunny@iehsas.com',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'is_active' => true,
            ]
        );
    }
}
