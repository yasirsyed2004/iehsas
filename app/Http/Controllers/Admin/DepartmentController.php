<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('head')
            ->withCount('tasks')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $admins = Admin::where('status', true)->orderBy('name')->get();
        return view('admin.departments.create', compact('admins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:admins,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully!');
    }

    public function show(Department $department)
    {
        $department->load(['head', 'tasks' => function($query) {
            $query->with(['assignedUser', 'assignedByAdmin'])
                ->orderBy('deadline')
                ->limit(10);
        }]);

        $stats = [
            'total_tasks' => $department->tasks()->count(),
            'pending' => $department->tasks()->where('status', 'pending')->count(),
            'in_progress' => $department->tasks()->where('status', 'in_progress')->count(),
            'completed' => $department->tasks()->where('status', 'completed')->count(),
            'overdue' => $department->tasks()->where('deadline', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled'])->count(),
        ];

        return view('admin.departments.show', compact('department', 'stats'));
    }

    public function edit(Department $department)
    {
        $admins = Admin::where('status', true)->orderBy('name')->get();
        return view('admin.departments.edit', compact('department', 'admins'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)],
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:admins,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully!');
    }

    public function destroy(Department $department)
    {
        if ($department->tasks()->count() > 0) {
            return back()->with('error', 'Cannot delete department that has tasks assigned to it.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully!');
    }

    public function toggleStatus(Department $department)
    {
        $department->update([
            'is_active' => !$department->is_active
        ]);

        $status = $department->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Department {$status} successfully!");
    }
}
