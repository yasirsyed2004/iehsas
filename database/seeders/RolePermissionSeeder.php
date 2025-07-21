<?php
// database/seeders/RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            'view_entry_tests',
            'create_entry_tests',
            'edit_entry_tests',
            'delete_entry_tests',
            'view_courses',
            'create_courses',
            'edit_courses',
            'delete_courses',
            'view_students',
            'manage_enrollments',
            'view_reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $instructorRole = Role::firstOrCreate(['name' => 'instructor']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        
        $instructorRole->givePermissionTo([
            'view_entry_tests',
            'create_entry_tests',
            'edit_entry_tests',
            'view_courses',
            'create_courses',
            'edit_courses',
            'view_students',
            'manage_enrollments',
            'view_reports',
        ]);

        $studentRole->givePermissionTo([
            'view_entry_tests',
            'view_courses',
        ]);

        // Create default admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@iehsas.com'
        ], [
            'name' => 'System Administrator',
            'password' => bcrypt('admin123'),
            'student_id' => 'ADMIN001',
        ]);
        $admin->assignRole('admin');

        // Create test instructor
        $instructor = User::firstOrCreate([
            'email' => 'instructor@iehsas.com'
        ], [
            'name' => 'Test Instructor',
            'password' => bcrypt('instructor123'),
            'student_id' => 'INST001',
        ]);
        $instructor->assignRole('instructor');

        // Create test student
        $student = User::firstOrCreate([
            'email' => 'student@iehsas.com'
        ], [
            'name' => 'Test Student',
            'password' => bcrypt('student123'),
            'student_id' => 'STU001',
        ]);
        $student->assignRole('student');
    }
}