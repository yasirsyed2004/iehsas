<?php
// File: app/Http/Controllers/Course/CourseController.php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        if (!$course->is_active) {
            abort(404);
        }

        return view('courses.show', compact('course'));
    }

    public function learn(Course $course)
    {
        // Check if user is enrolled
        $enrollment = auth()->user()->courseEnrollments()
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'You need to enroll in this course first.');
        }

        return view('courses.learn', compact('course', 'enrollment'));
    }
}