<?php
// File: app/Http/Controllers/Course/EnrollmentController.php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function enroll(Course $course)
    {
        $user = auth()->user();
        
        // Check if already enrolled
        $existingEnrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return back()->with('info', 'You are already enrolled in this course.');
        }

        // Check if entry test is required and user has passed
        if ($course->requires_entry_test) {
            $hasPassedTest = $user->entryTestAttempts()
                ->completed()
                ->passed()
                ->exists();

            if (!$hasPassedTest) {
                return back()->with('error', 'You need to pass the entry test before enrolling in this course.');
            }
        }

        // Create enrollment
        CourseEnrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'status' => 'enrolled'
        ]);

        return back()->with('success', 'Successfully enrolled in the course!');
    }

    public function myCourses()
    {
        $enrollments = auth()->user()->courseEnrollments()
            ->with('course')
            ->latest()
            ->paginate(10);

        return view('courses.my-courses', compact('enrollments'));
    }
}