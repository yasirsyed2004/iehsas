<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
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

    public function index()
    {
        $roles = Role::where('guard_name', 'admin')
            ->withCount('permissions')
            ->get()
            ->sortBy(function ($role) {
                // System roles first, then custom alphabetically
                $order = ['super_admin' => 0, 'admin' => 1, 'moderator' => 2];
                return $order[$role->name] ?? (3 + $role->id);
            })->values();

        $totalPermissions = Permission::where('guard_name', 'admin')->count();

        // Get all admins with their roles for the admin assignment section
        $admins = Admin::orderBy('name')->get();
        $allRoles = Role::where('guard_name', 'admin')->orderBy('name')->get();

        return view('admin.roles.index', compact('roles', 'totalPermissions', 'admins', 'allRoles'));
    }

    public function create()
    {
        $modules = $this->getPermissionModules();
        $allPermissions = Permission::where('guard_name', 'admin')->get()->keyBy('name');

        return view('admin.roles.create', compact('modules', 'allPermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z][a-z0-9_]*$/',
                'unique:roles,name',
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'Role name is required.',
            'name.regex' => 'Role name must start with a letter and contain only lowercase letters, numbers, and underscores.',
            'name.unique' => 'A role with this name already exists.',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'admin',
        ]);

        $permissions = $validated['permissions'] ?? [];
        $validPermissions = Permission::where('guard_name', 'admin')
            ->whereIn('name', $permissions)
            ->pluck('name')
            ->toArray();

        $role->syncPermissions($validPermissions);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" created successfully with " . count($validPermissions) . " permissions.");
    }

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

    public function update(Request $request, Role $role)
    {
        if ($role->guard_name !== 'admin') {
            abort(404);
        }

        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Super Admin role permissions cannot be modified.');
        }

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $permissions = $validated['permissions'] ?? [];
        $validPermissions = Permission::where('guard_name', 'admin')
            ->whereIn('name', $permissions)
            ->pluck('name')
            ->toArray();

        $role->syncPermissions($validPermissions);

        return redirect()->route('admin.roles.index')
            ->with('success', "Permissions for \"{$role->name}\" role updated successfully.");
    }

    public function destroy(Role $role)
    {
        if ($role->guard_name !== 'admin') {
            abort(404);
        }

        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Super Admin role cannot be deleted.');
        }

        // Check if any admins are assigned this role
        $adminCount = Admin::where('role', $role->name)->count();
        if ($adminCount > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', "Cannot delete \"{$role->name}\" role — {$adminCount} admin(s) are still assigned to it.");
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$roleName}\" deleted successfully.");
    }

    public function assignRole(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $currentAdmin = auth('admin')->user();
        if ($validated['role'] === 'super_admin' && $currentAdmin->role !== 'super_admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Only Super Admins can assign the Super Admin role.');
        }

        $admin->update(['role' => $validated['role']]);
        $admin->syncRoles([$validated['role']]);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role for \"{$admin->name}\" changed to \"{$validated['role']}\" successfully.");
    }

    // ==================== ADMIN ACCOUNT CRUD ====================

    public function createAdmin()
    {
        $roles = Role::where('guard_name', 'admin')->orderBy('name')->get();
        return view('admin.roles.create-admin', compact('roles'));
    }

    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|string|exists:roles,name',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|boolean',
        ]);

        $currentAdmin = auth('admin')->user();
        if ($validated['role'] === 'super_admin' && $currentAdmin->role !== 'super_admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Only Super Admins can create Super Admin accounts.');
        }

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
        ]);

        $admin->syncRoles([$validated['role']]);

        return redirect()->route('admin.roles.index')
            ->with('success', "Admin account \"{$admin->name}\" created with role \"{$validated['role']}\".");
    }

    public function editAdmin(Admin $admin)
    {
        $roles = Role::where('guard_name', 'admin')->orderBy('name')->get();
        return view('admin.roles.edit-admin', compact('admin', 'roles'));
    }

    public function updateAdmin(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => 'required|string|exists:roles,name',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|boolean',
        ]);

        $currentAdmin = auth('admin')->user();
        if ($validated['role'] === 'super_admin' && $currentAdmin->role !== 'super_admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Only Super Admins can assign the Super Admin role.');
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $admin->update($data);
        $admin->syncRoles([$validated['role']]);

        return redirect()->route('admin.roles.index')
            ->with('success', "Admin account \"{$admin->name}\" updated successfully.");
    }

    public function destroyAdmin(Admin $admin)
    {
        if ($admin->role === 'super_admin') {
            $superAdminCount = Admin::where('role', 'super_admin')->count();
            if ($superAdminCount <= 1) {
                return redirect()->route('admin.roles.index')
                    ->with('error', 'Cannot delete the last Super Admin account.');
            }
        }

        if ($admin->id === auth('admin')->id()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $adminName = $admin->name;
        $admin->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Admin account \"{$adminName}\" deleted successfully.");
    }

    public function toggleAdminStatus(Admin $admin)
    {
        if ($admin->id === auth('admin')->id()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'You cannot deactivate your own account.');
        }

        $admin->update(['status' => !$admin->status]);
        $status = $admin->status ? 'activated' : 'deactivated';

        return redirect()->route('admin.roles.index')
            ->with('success', "Admin \"{$admin->name}\" has been {$status}.");
    }
}
