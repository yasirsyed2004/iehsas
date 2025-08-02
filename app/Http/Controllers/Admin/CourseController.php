<?php
// File: app/Http/Controllers/Admin/CourseController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['category', 'instructor'])
            ->withCount(['enrollments', 'modules', 'lessons']);

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = CourseCategory::active()->ordered()->get();

        return view('admin.courses.index', compact('courses', 'categories'));
    }

    public function create()
    {
        $categories = CourseCategory::active()->ordered()->get();
        $instructors = User::where('role', 'teacher')
            ->orWhere('role', 'admin')
            ->orderBy('name')
            ->get();

        return view('admin.courses.create', compact('categories', 'instructors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:courses,title',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:course_categories,id',
            'instructor_id' => 'nullable|exists:users,id',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'duration_hours' => 'required|integer|min:1',
            'max_students' => 'nullable|integer|min:1',
            'requires_entry_test' => 'boolean',
            'min_entry_test_score' => 'nullable|numeric|min:0|max:100',
            'has_certificate' => 'boolean',
            'learning_outcomes' => 'nullable|array',
            'learning_outcomes.*' => 'string|max:255',
            'requirements' => 'nullable|string',
            'is_featured' => 'boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        // Handle file uploads
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('courses/banners', 'public');
        }

        // Set status
        $validated['status'] = $request->boolean('publish_immediately') ? 'published' : 'draft';
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        $course = Course::create($validated);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        $course->load([
            'category',
            'instructor',
            'modules.lessons',
            'enrollments.user',
            'reviews.user'
        ]);

        $stats = [
            'total_enrollments' => $course->enrollments()->count(),
            'active_enrollments' => $course->activeEnrollments()->count(),
            'completed_enrollments' => $course->enrollments()->where('status', 'completed')->count(),
            'average_rating' => $course->average_rating,
            'total_reviews' => $course->reviews_count,
            'total_modules' => $course->total_modules,
            'total_lessons' => $course->total_lessons,
            'total_duration' => $course->duration_hours
        ];

        return view('admin.courses.show', compact('course', 'stats'));
    }

    public function edit(Course $course)
    {
        $categories = CourseCategory::active()->ordered()->get();
        $instructors = User::where('role', 'teacher')
            ->orWhere('role', 'admin')
            ->orderBy('name')
            ->get();

        return view('admin.courses.edit', compact('course', 'categories', 'instructors'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', Rule::unique('courses')->ignore($course->id)],
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:course_categories,id',
            'instructor_id' => 'nullable|exists:users,id',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'duration_hours' => 'required|integer|min:1',
            'max_students' => 'nullable|integer|min:1',
            'requires_entry_test' => 'boolean',
            'min_entry_test_score' => 'nullable|numeric|min:0|max:100',
            'has_certificate' => 'boolean',
            'learning_outcomes' => 'nullable|array',
            'learning_outcomes.*' => 'string|max:255',
            'requirements' => 'nullable|string',
            'is_featured' => 'boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        // Handle file uploads
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        if ($request->hasFile('banner_image')) {
            // Delete old banner
            if ($course->banner_image) {
                Storage::disk('public')->delete($course->banner_image);
            }
            $validated['banner_image'] = $request->file('banner_image')->store('courses/banners', 'public');
        }

        $course->update($validated);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        // Check if course has enrollments
        if ($course->enrollments()->count() > 0) {
            return back()->with('error', 'Cannot delete course with active enrollments. Archive it instead.');
        }

        // Delete files
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }
        if ($course->banner_image) {
            Storage::disk('public')->delete($course->banner_image);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully!');
    }

    public function toggleStatus(Course $course)
    {
        $course->update([
            'is_active' => !$course->is_active
        ]);

        $status = $course->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Course {$status} successfully!");
    }

    public function updateStatus(Request $request, Course $course)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,published,archived'
        ]);

        $updates = ['status' => $validated['status']];
        
        if ($validated['status'] === 'published' && !$course->published_at) {
            $updates['published_at'] = now();
        }

        $course->update($updates);

        return back()->with('success', 'Course status updated successfully!');
    }

    public function duplicate(Course $course)
    {
        $newCourse = $course->replicate();
        $newCourse->title = $course->title . ' (Copy)';
        $newCourse->slug = null; // Will be auto-generated
        $newCourse->status = 'draft';
        $newCourse->published_at = null;
        $newCourse->is_featured = false;
        $newCourse->save();

        // Duplicate modules and lessons
        foreach ($course->modules as $module) {
            $newModule = $module->replicate();
            $newModule->course_id = $newCourse->id;
            $newModule->save();

            foreach ($module->lessons as $lesson) {
                $newLesson = $lesson->replicate();
                $newLesson->course_module_id = $newModule->id;
                $newLesson->save();
            }
        }

        return redirect()->route('admin.courses.edit', $newCourse)
            ->with('success', 'Course duplicated successfully! You can now edit the copy.');
    }
}