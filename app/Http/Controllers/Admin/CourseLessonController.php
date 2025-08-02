<?php
// File: app/Http/Controllers/Admin/CourseLessonController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseLessonController extends Controller
{
    public function index(Course $course, CourseModule $module)
    {
        $module->load('lessons');
        
        return view('admin.courses.lessons.index', compact('course', 'module'));
    }

    public function create(Course $course, CourseModule $module)
    {
        return view('admin.courses.lessons.create', compact('course', 'module'));
    }

    public function store(Request $request, Course $course, CourseModule $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:video,text,quiz,assignment',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'file_path' => 'nullable|file|max:50000', // 50MB max
            'duration_minutes' => 'required|integer|min:1',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        // Handle file upload
        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('courses/lessons', 'public');
        }

        $validated['course_module_id'] = $module->id;
        
        CourseLesson::create($validated);

        return redirect()->route('admin.courses.modules.lessons.index', [$course, $module])
            ->with('success', 'Lesson created successfully!');
    }

    public function show(Course $course, CourseModule $module, CourseLesson $lesson)
    {
        return view('admin.courses.lessons.show', compact('course', 'module', 'lesson'));
    }

    public function edit(Course $course, CourseModule $module, CourseLesson $lesson)
    {
        return view('admin.courses.lessons.edit', compact('course', 'module', 'lesson'));
    }

    public function update(Request $request, Course $course, CourseModule $module, CourseLesson $lesson)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:video,text,quiz,assignment',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'file_path' => 'nullable|file|max:50000',
            'duration_minutes' => 'required|integer|min:1',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        // Handle file upload
        if ($request->hasFile('file_path')) {
            // Delete old file
            if ($lesson->file_path) {
                Storage::disk('public')->delete($lesson->file_path);
            }
            $validated['file_path'] = $request->file('file_path')->store('courses/lessons', 'public');
        }

        $lesson->update($validated);

        return redirect()->route('admin.courses.modules.lessons.index', [$course, $module])
            ->with('success', 'Lesson updated successfully!');
    }

    public function destroy(Course $course, CourseModule $module, CourseLesson $lesson)
    {
        // Delete associated file
        if ($lesson->file_path) {
            Storage::disk('public')->delete($lesson->file_path);
        }

        $lesson->delete();

        return redirect()->route('admin.courses.modules.lessons.index', [$course, $module])
            ->with('success', 'Lesson deleted successfully!');
    }

    public function reorder(Request $request, Course $course, CourseModule $module)
    {
        $request->validate([
            'lessons' => 'required|array',
            'lessons.*.id' => 'required|exists:course_lessons,id',
            'lessons.*.order' => 'required|integer|min:0'
        ]);

        foreach ($request->lessons as $lessonData) {
            CourseLesson::where('id', $lessonData['id'])
                ->where('course_module_id', $module->id)
                ->update(['order' => $lessonData['order']]);
        }

        return response()->json(['success' => true, 'message' => 'Lessons reordered successfully!']);
    }
}