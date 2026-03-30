<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Admin;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'admin';

        // Define all permissions grouped by module
        $modules = [
            'Dashboard' => ['view_dashboard'],
            'Users' => ['view_users', 'create_users', 'edit_users', 'delete_users'],
            'Entry Tests' => ['view_entry_tests', 'create_entry_tests', 'edit_entry_tests', 'delete_entry_tests'],
            'Questions' => ['view_questions', 'create_questions', 'edit_questions', 'delete_questions'],
            'Student Attempts' => ['view_student_attempts', 'manage_student_attempts'],
            'Registered Students' => ['view_registered_students', 'manage_registered_students'],
            'Courses' => ['view_courses', 'create_courses', 'edit_courses', 'delete_courses'],
            'Course Categories' => ['view_course_categories', 'create_course_categories', 'edit_course_categories', 'delete_course_categories'],
            'Enrollments' => ['view_enrollments', 'manage_enrollments'],
            'Departments' => ['view_departments', 'create_departments', 'edit_departments', 'delete_departments'],
            'Tasks' => ['view_tasks', 'create_tasks', 'edit_tasks', 'delete_tasks'],
            'Daily Reports' => ['view_dar', 'manage_dar'],
            'Notifications' => ['view_notifications'],
            'Roles & Permissions' => ['manage_roles_permissions'],
        ];

        // Create all permissions
        $allPermissions = [];
        foreach ($modules as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => $guard,
                ]);
                $allPermissions[] = $permission;
            }
        }

        // Create roles and assign permissions
        // 1. Super Admin — gets all permissions (also bypassed via Gate::before)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => $guard]);
        $superAdminRole->syncPermissions($allPermissions);

        // 2. Admin — gets all except manage_roles_permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $adminPermissions = array_filter($allPermissions, fn($p) => $p !== 'manage_roles_permissions');
        $adminRole->syncPermissions($adminPermissions);

        // 3. Moderator — gets only view permissions
        $moderatorRole = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => $guard]);
        $viewPermissions = array_filter($allPermissions, fn($p) => str_starts_with($p, 'view_'));
        $moderatorRole->syncPermissions($viewPermissions);

        // Sync Spatie roles to existing Admin records based on their string role field
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $roleName = $admin->role ?? 'moderator';
            if (Role::where('name', $roleName)->where('guard_name', $guard)->exists()) {
                $admin->syncRoles([$roleName]);
            }
        }

        $this->command->info('Roles & Permissions seeded successfully!');
        $this->command->info('Permissions: ' . count($allPermissions));
        $this->command->info('Roles: super_admin, admin, moderator');
        $this->command->info('Synced ' . $admins->count() . ' admin(s) to their Spatie roles.');
    }
}
