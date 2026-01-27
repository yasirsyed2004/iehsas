<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\EntryTestAttempt;
use Illuminate\Http\Request;

class RegisteredStudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['latestAttempt.entryTest', 'selectedCourses']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('contact_number', 'like', '%' . $search . '%')
                  ->orWhere('id_number', 'like', '%' . $search . '%');
            });
        }

        // Test status filter
        if ($request->filled('test_status')) {
            switch ($request->test_status) {
                case 'passed':
                    $query->whereHas('entryTestAttempts', function($q) {
                        $q->where('status', 'completed')
                          ->whereNotNull('percentage')
                          ->whereRaw('percentage >= (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)');
                    });
                    break;
                case 'failed':
                    $query->whereHas('entryTestAttempts', function($q) {
                        $q->where('status', 'completed')
                          ->whereNotNull('percentage')
                          ->whereRaw('percentage < (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)');
                    });
                    break;
                case 'not_attempted':
                    $query->whereDoesntHave('entryTestAttempts', function($q) {
                        $q->where('status', 'completed');
                    });
                    break;
                case 'in_progress':
                    $query->whereHas('entryTestAttempts', function($q) {
                        $q->where('status', 'in_progress');
                    });
                    break;
            }
        }

        // Enrollment status filter
        if ($request->filled('enrollment_status')) {
            switch ($request->enrollment_status) {
                case 'eligible':
                    $query->eligibleForEnrollment();
                    break;
                case 'completed':
                    $query->enrollmentCompleted();
                    break;
                case 'in_progress':
                    $query->where('enrollment_code_used', true)
                          ->where('enrollment_form_submitted', false);
                    break;
                case 'code_sent':
                    $query->whereNotNull('enrollment_verification_code')
                          ->where('enrollment_code_used', false);
                    break;
            }
        }

        // Handle pagination - allow viewing all or paginated
        if ($request->get('per_page') === 'all') {
            $students = $query->orderBy('created_at', 'desc')->paginate($query->count() ?: 15);
        } else {
            $students = $query->orderBy('created_at', 'desc')->paginate(15);
        }

        // Statistics
        $stats = [
            'total' => Student::count(),
            'passed' => Student::whereHas('entryTestAttempts', function($q) {
                $q->where('status', 'completed')
                  ->whereNotNull('percentage')
                  ->whereRaw('percentage >= (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)');
            })->count(),
            'failed' => Student::whereHas('entryTestAttempts', function($q) {
                $q->where('status', 'completed')
                  ->whereNotNull('percentage')
                  ->whereRaw('percentage < (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)');
            })->count(),
            'eligible' => Student::eligibleForEnrollment()->count(),
            'enrolled' => Student::enrollmentCompleted()->count(),
        ];

        return view('admin.registered-students.index', compact('students', 'stats'));
    }

    public function show(Student $student)
    {
        $student->load([
            'entryTestAttempts' => function($query) {
                $query->with('entryTest')->orderBy('created_at', 'desc');
            },
            'selectedCourses',
            'enrollmentDocuments'
        ]);

        // Get test statistics
        $testStats = [
            'total_attempts' => $student->entryTestAttempts->count(),
            'completed_attempts' => $student->entryTestAttempts->where('status', 'completed')->count(),
            'best_score' => $student->entryTestAttempts->where('status', 'completed')->max('percentage') ?? 0,
            'latest_score' => $student->latestAttempt?->percentage ?? 0,
            'passed' => $student->hasPassedEntryTest(),
        ];

        // Get document summary
        $documentSummary = $student->getDocumentStatusSummary();

        return view('admin.registered-students.show', compact('student', 'testStats', 'documentSummary'));
    }

    public function toggleRetake(Student $student)
    {
        $student->update([
            'is_retake_allowed' => !$student->is_retake_allowed
        ]);

        $status = $student->is_retake_allowed ? 'allowed' : 'disallowed';
        return back()->with('success', "Retake {$status} for {$student->full_name}");
    }

    public function destroy(Student $student)
    {
        // Check if student has completed enrollment
        if ($student->enrollment_form_submitted) {
            return back()->with('error', 'Cannot delete student who has completed enrollment.');
        }

        // Delete related records
        $student->entryTestAttempts()->delete();
        $student->enrollmentDocuments()->delete();
        $student->selectedCourses()->detach();
        $student->delete();

        return redirect()->route('admin.registered-students.index')
            ->with('success', 'Student deleted successfully!');
    }

    public function export(Request $request)
    {
        $query = Student::with(['latestAttempt.entryTest', 'selectedCourses']);

        // Apply same filters as index
        if ($request->filled('test_status')) {
            switch ($request->test_status) {
                case 'passed':
                    $query->whereHas('entryTestAttempts', function($q) {
                        $q->where('status', 'completed')
                          ->whereNotNull('percentage')
                          ->whereRaw('percentage >= (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)');
                    });
                    break;
                case 'failed':
                    $query->whereHas('entryTestAttempts', function($q) {
                        $q->where('status', 'completed')
                          ->whereNotNull('percentage')
                          ->whereRaw('percentage < (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)');
                    });
                    break;
            }
        }

        $students = $query->orderBy('created_at', 'desc')->get();

        $filename = 'registered_students_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'ID', 'Full Name', 'Father Name', 'Email', 'Contact',
                'ID Type', 'ID Number', 'Gender', 'Nationality', 'Qualification',
                'Test Status', 'Score (%)', 'Enrollment Status', 'Registered At'
            ]);

            foreach ($students as $student) {
                $testStatus = 'Not Attempted';
                $score = '-';

                if ($student->latestAttempt) {
                    if ($student->latestAttempt->status === 'completed') {
                        $testStatus = $student->latestAttempt->passed ? 'Passed' : 'Failed';
                        $score = $student->latestAttempt->percentage . '%';
                    } else {
                        $testStatus = 'In Progress';
                    }
                }

                fputcsv($file, [
                    $student->id,
                    $student->full_name,
                    $student->father_name,
                    $student->email,
                    $student->contact_number,
                    $student->id_type_label,
                    $student->id_number,
                    ucfirst($student->gender),
                    $student->nationality,
                    $student->qualification,
                    $testStatus,
                    $score,
                    $student->enrollment_status_label,
                    $student->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
