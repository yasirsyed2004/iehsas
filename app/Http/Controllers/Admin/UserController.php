<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Apply filters
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('student_id', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:admin,teacher,student',
            'phone' => 'nullable|string|max:20',
            'student_id' => 'nullable|string|max:50|unique:users,student_id',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'status' => 'required|boolean'
        ]);

        // Generate student ID if role is student and no ID provided
        $data = $request->all();
        if ($request->role === 'student' && !$request->student_id) {
            $data['student_id'] = $this->generateStudentId();
        }

        // Hash password
        $data['password'] = Hash::make($request->password);

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => 'required|in:admin,teacher,student',
            'phone' => 'nullable|string|max:20',
            'student_id' => 'nullable|string|max:50|unique:users,student_id,' . $user->id,
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'status' => 'required|boolean'
        ]);

        $data = $request->except(['password', 'password_confirmation']);
        
        // Update password only if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Generate student ID if changing role to student and no ID exists
        if ($request->role === 'student' && !$user->student_id && !$request->student_id) {
            $data['student_id'] = $this->generateStudentId();
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        // Prevent deletion of the last admin
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Cannot delete the last admin user!');
            }
        }

        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    public function toggleStatus(User $user)
    {
        // Prevent deactivating the last admin
        if ($user->role === 'admin' && $user->status) {
            $activeAdminCount = User::where('role', 'admin')->where('status', true)->count();
            if ($activeAdminCount <= 1) {
                return back()->with('error', 'Cannot deactivate the last active admin user!');
            }
        }

        $user->update(['status' => !$user->status]);
        
        $status = $user->status ? 'activated' : 'deactivated';
        
        return back()->with('success', "User {$status} successfully!");
    }

    private function generateStudentId()
    {
        $year = date('Y');
        $lastStudent = User::where('role', 'student')
            ->where('student_id', 'like', $year . '%')
            ->orderBy('student_id', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->student_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}