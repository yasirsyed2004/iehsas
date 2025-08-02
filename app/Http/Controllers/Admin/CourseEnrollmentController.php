<?php
// File: app/Http/Controllers/Admin/CourseEnrollmentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseEnrollmentController extends Controller
{
    public function index(Course $course)
    {
        $enrollments = $course->enrollments()
            ->with(['user', 'entryTestAttempt'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_enrollments' => $course->enrollments()->count(),
            'active_enrollments' => $course->enrollments()->whereIn('status', ['enrolled', 'active'])->count(),
            'completed_enrollments' => $course->enrollments()->where('status', 'completed')->count(),
            'dropped_enrollments' => $course->enrollments()->where('status', 'dropped')->count(),
            'average_progress' => $course->enrollments()->avg('progress_percentage') ?? 0
        ];

        return view('admin.courses.enrollments.index', compact('course', 'enrollments', 'stats'));
    }

    public function enrollStudent(Request $request, Course $course)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'skip_entry_test' => 'boolean'
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Check if already enrolled
        if ($course->enrollments()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User is already enrolled in this course.');
        }

        // Check entry test requirement (unless admin overrides)
        if ($course->requires_entry_test && !$request->boolean('skip_entry_test')) {
            $hasPassedTest = $user->entryTestAttempts()
                ->where('status', 'completed')
                ->where('percentage', '>=', $course->min_entry_test_score ?? 60)
                ->exists();

            if (!$hasPassedTest) {
                return back()->with('error', 'User has not passed the required entry test.');
            }
        }

        // Check enrollment limit
        if ($course->max_students && $course->enrollment_count >= $course->max_students) {
            return back()->with('error', 'Course has reached maximum student capacity.');
        }

        // Create enrollment
        CourseEnrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'status' => 'enrolled'
        ]);

        return back()->with('success', "Successfully enrolled {$user->name} in the course!");
    }

    public function updateStatus(Request $request, Course $course, CourseEnrollment $enrollment)
    {
        $validated = $request->validate([
            'status' => 'required|in:enrolled,active,completed,dropped'
        ]);

        $updates = ['status' => $validated['status']];

        // Set appropriate timestamps
        switch ($validated['status']) {
            case 'active':
                if (!$enrollment->started_at) {
                    $updates['started_at'] = now();
                }
                break;
            case 'completed':
                $updates['completed_at'] = now();
                $updates['progress_percentage'] = 100;
                break;
            case 'dropped':
                // Keep existing progress but mark as dropped
                break;
        }

        $enrollment->update($updates);

        return back()->with('success', 'Enrollment status updated successfully!');
    }

    public function destroy(Course $course, CourseEnrollment $enrollment)
    {
        $userName = $enrollment->user->name;
        $enrollment->delete();

        return back()->with('success', "Removed {$userName} from the course.");
    }

    public function allEnrollments(Request $request)
    {
        $query = CourseEnrollment::with(['user', 'course', 'entryTestAttempt']);

        // Apply filters
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);
        $courses = Course::published()->orderBy('title')->get();

        $stats = [
            'total_enrollments' => CourseEnrollment::count(),
            'active_enrollments' => CourseEnrollment::whereIn('status', ['enrolled', 'active'])->count(),
            'completed_enrollments' => CourseEnrollment::where('status', 'completed')->count(),
            'completion_rate' => CourseEnrollment::count() > 0 ? 
                round((CourseEnrollment::where('status', 'completed')->count() / CourseEnrollment::count()) * 100, 1) : 0
        ];

        return view('admin.enrollments.index', compact('enrollments', 'courses', 'stats'));
    }

    public function bulkEnrollForm()
    {
        $courses = Course::published()->orderBy('title')->get();
        $users = User::students()->active()->orderBy('name')->get();

        return view('admin.enrollments.bulk-enroll', compact('courses', 'users'));
    }

    public function bulkEnroll(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'skip_entry_test' => 'boolean',
            'notify_students' => 'boolean'
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $users = User::whereIn('id', $validated['user_ids'])->get();
        
        $enrolled = [];
        $skipped = [];
        $errors = [];

        DB::transaction(function() use ($course, $users, $request, &$enrolled, &$skipped, &$errors) {
            foreach ($users as $user) {
                // Check if already enrolled
                if ($course->enrollments()->where('user_id', $user->id)->exists()) {
                    $skipped[] = $user->name . ' (already enrolled)';
                    continue;
                }

                // Check entry test requirement
                if ($course->requires_entry_test && !$request->boolean('skip_entry_test')) {
                    $hasPassedTest = $user->hasPassedEntryTest($course->min_entry_test_score ?? 60);
                    if (!$hasPassedTest) {
                        $skipped[] = $user->name . ' (entry test not passed)';
                        continue;
                    }
                }

                // Check enrollment limit
                if ($course->max_students && $course->enrollment_count >= $course->max_students) {
                    $errors[] = 'Course has reached maximum capacity';
                    break;
                }

                // Create enrollment
                CourseEnrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'enrolled_at' => now(),
                    'status' => 'enrolled'
                ]);

                $enrolled[] = $user->name;

                // TODO: Send notification email if requested
                if ($request->boolean('notify_students')) {
                    // Implement email notification
                }
            }
        });

        $message = count($enrolled) . ' students enrolled successfully.';
        if (count($skipped) > 0) {
            $message .= ' Skipped: ' . implode(', ', $skipped);
        }
        if (count($errors) > 0) {
            $message .= ' Errors: ' . implode(', ', $errors);
        }

        return redirect()->route('admin.enrollments.index')
            ->with('success', $message);
    }

    public function export(Request $request)
    {
        $query = CourseEnrollment::with(['user', 'course']);

        // Apply same filters as index
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'course_enrollments_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function() use ($enrollments) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Student Name',
                'Student Email',
                'Course Title',
                'Course Category',
                'Enrollment Date',
                'Status',
                'Progress (%)',
                'Started At',
                'Completed At'
            ]);

            // CSV Data
            foreach ($enrollments as $enrollment) {
                fputcsv($file, [
                    $enrollment->user->name,
                    $enrollment->user->email,
                    $enrollment->course->title,
                    $enrollment->course->category->name,
                    $enrollment->enrolled_at->format('Y-m-d'),
                    ucfirst($enrollment->status),
                    $enrollment->progress_percentage,
                    $enrollment->started_at ? $enrollment->started_at->format('Y-m-d H:i') : '',
                    $enrollment->completed_at ? $enrollment->completed_at->format('Y-m-d H:i') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}