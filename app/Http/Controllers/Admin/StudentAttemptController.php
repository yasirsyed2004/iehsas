<?php
// File: app/Http/Controllers/Admin/StudentAttemptController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EntryTestAttempt;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentAttemptController extends Controller
{
    public function index(Request $request)
    {
        $query = EntryTestAttempt::with(['entryTest', 'student'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by test result
        if ($request->filled('result')) {
            if ($request->result === 'passed') {
                $query->passed();
            } elseif ($request->result === 'failed') {
                $query->whereRaw('percentage < (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)');
            }
        }

        // Search by student name or CNIC
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('cnic', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $attempts = $query->paginate(15);

        // Statistics
        $stats = [
            'total_attempts' => EntryTestAttempt::count(),
            'completed_attempts' => EntryTestAttempt::where('status', 'completed')->count(),
            'passed_attempts' => EntryTestAttempt::completed()->passed()->count(),
            'in_progress' => EntryTestAttempt::where('status', 'in_progress')->count(),
            'average_score' => EntryTestAttempt::completed()->avg('percentage')
        ];

        return view('admin.student-attempts.index', compact('attempts', 'stats'));
    }

    public function show(EntryTestAttempt $attempt)
    {
        $attempt->load(['entryTest', 'student', 'answers.question']);
        
        return view('admin.student-attempts.show', compact('attempt'));
    }

    public function allowRetake(EntryTestAttempt $attempt)
    {
        $student = $attempt->student;
        
        // Allow retake by updating student record
        $student->update(['is_retake_allowed' => true]);

        return back()->with('success', 'Retake permission granted for ' . $student->full_name);
    }

    public function destroy(EntryTestAttempt $attempt)
    {
        $studentName = $attempt->student->full_name;
        $attempt->delete();

        return redirect()->route('admin.student-attempts.index')
            ->with('success', "Attempt by {$studentName} has been deleted successfully.");
    }
}