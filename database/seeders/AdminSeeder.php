<?php
// File: database/seeders/AdminSeeder.php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create Super Admin
        Admin::firstOrCreate([
            'email' => 'admin@iehsas.com'
        ], [
            'name' => 'Super Administrator',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'status' => true,
            'phone' => '+92-300-1234567'
        ]);

        // Create Regular Admin
        Admin::firstOrCreate([
            'email' => 'admin2@iehsas.com'
        ], [
            'name' => 'System Administrator',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => true,
            'phone' => '+92-300-7654321'
        ]);

        // Create Moderator
        Admin::firstOrCreate([
            'email' => 'moderator@iehsas.com'
        ], [
            'name' => 'Content Moderator',
            'password' => Hash::make('mod123'),
            'role' => 'moderator',
            'status' => true,
            'phone' => '+92-301-1234567'
        ]);
    }
}