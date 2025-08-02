<?php
// File: app/Http/Controllers/Admin/CourseModuleController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use Illuminate\Http\Request;

class CourseModuleController extends Controller
{
    public function index(Course $course)
    {
        $course->load('modules.lessons');
        
        return view('admin.courses.modules.index', compact('course'));
    }

    public function create(Course $course)
    {
        return view('admin.courses.modules.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['course_id'] = $course->id;
        
        CourseModule::create($validated);

        return redirect()->route('admin.courses.modules.index', $course)
            ->with('success', 'Module created successfully!');
    }

    public function show(Course $course, CourseModule $module)
    {
        $module->load('lessons');
        
        return view('admin.courses.modules.show', compact('course', 'module'));
    }

    public function edit(Course $course, CourseModule $module)
    {
        return view('admin.courses.modules.edit', compact('course', 'module'));
    }

    public function update(Request $request, Course $course, CourseModule $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $module->update($validated);

        return redirect()->route('admin.courses.modules.index', $course)
            ->with('success', 'Module updated successfully!');
    }

    public function destroy(Course $course, CourseModule $module)
    {
        $module->delete();

        return redirect()->route('admin.courses.modules.index', $course)
            ->with('success', 'Module deleted successfully!');
    }

    public function reorder(Request $request, Course $course)
    {
        $request->validate([
            'modules' => 'required|array',
            'modules.*.id' => 'required|exists:course_modules,id',
            'modules.*.order' => 'required|integer|min:0'
        ]);

        foreach ($request->modules as $moduleData) {
            CourseModule::where('id', $moduleData['id'])
                ->where('course_id', $course->id)
                ->update(['order' => $moduleData['order']]);
        }

        return response()->json(['success' => true, 'message' => 'Modules reordered successfully!']);
    }
}