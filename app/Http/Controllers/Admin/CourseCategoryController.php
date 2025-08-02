<?php
// File: app/Http/Controllers/Admin/CourseCategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseCategoryController extends Controller
{
    public function index()
    {
        $categories = CourseCategory::withCount('courses')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.course-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.course-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:course_categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        CourseCategory::create($validated);

        return redirect()->route('admin.course-categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function show(CourseCategory $courseCategory)
    {
        $courseCategory->load('courses.instructor');
        
        return view('admin.course-categories.show', compact('courseCategory'));
    }

    public function edit(CourseCategory $courseCategory)
    {
        return view('admin.course-categories.edit', compact('courseCategory'));
    }

    public function update(Request $request, CourseCategory $courseCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('course_categories')->ignore($courseCategory->id)],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $courseCategory->update($validated);

        return redirect()->route('admin.course-categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(CourseCategory $courseCategory)
    {
        // Check if category has courses
        if ($courseCategory->courses()->count() > 0) {
            return back()->with('error', 'Cannot delete category that has courses assigned to it.');
        }

        $courseCategory->delete();

        return redirect()->route('admin.course-categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    public function toggleStatus(CourseCategory $courseCategory)
    {
        $courseCategory->update([
            'is_active' => !$courseCategory->is_active
        ]);

        $status = $courseCategory->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Category {$status} successfully!");
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:course_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->categories as $categoryData) {
            CourseCategory::where('id', $categoryData['id'])
                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Categories reordered successfully!']);
    }
}