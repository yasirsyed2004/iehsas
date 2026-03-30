<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    /**
     * Permission grouping by module for the UI.
     */
    private function getPermissionModules(): array
    {
        return [
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
    }

    /**
     * Display all roles with their permission counts.
     */
    public function index()
    {
        $roles = Role::where('guard_name', 'admin')
            ->withCount('permissions')
            ->orderByRaw("FIELD(name, 'super_admin', 'admin', 'moderator')")
            ->get();

        $totalPermissions = Permission::where('guard_name', 'admin')->count();

        return view('admin.roles.index', compact('roles', 'totalPermissions'));
    }

    /**
     * Show the permission edit form for a role.
     */
    public function edit(Role $role)
    {
        if ($role->guard_name !== 'admin') {
            abort(404);
        }

        $modules = $this->getPermissionModules();
        $allPermissions = Permission::where('guard_name', 'admin')->get()->keyBy('name');
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'modules', 'allPermissions', 'rolePermissions'));
    }

    /**
     * Update permissions for a role.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->guard_name !== 'admin') {
            abort(404);
        }

        // Super admin role cannot be edited
        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Super Admin role permissions cannot be modified.');
        }

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $permissions = $validated['permissions'] ?? [];

        // Only sync permissions that belong to the admin guard
        $validPermissions = Permission::where('guard_name', 'admin')
            ->whereIn('name', $permissions)
            ->pluck('name')
            ->toArray();

        $role->syncPermissions($validPermissions);

        return redirect()->route('admin.roles.index')
            ->with('success', "Permissions for \"{$role->name}\" role updated successfully.");
    }
}
