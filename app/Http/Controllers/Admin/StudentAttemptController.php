<?php
// File: app/Http/Controllers/Admin/StudentAttemptController.php
// FIXED: Updated search to use new ID fields instead of old CNIC field

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

        // FIXED: Search by student name, email, or ID number (instead of old CNIC)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%")  // FIXED: Use id_number instead of cnic
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%");
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
        $attempt->load(['entryTest', 'student.selectedCourses', 'answers.question']);
        
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

    /**
     * Export student attempts to CSV
     */
    public function export(Request $request)
    {
        $query = EntryTestAttempt::with(['entryTest', 'student'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('result')) {
            if ($request->result === 'passed') {
                $query->passed();
            } elseif ($request->result === 'failed') {
                $query->whereRaw('percentage < (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%")  // FIXED: Use id_number instead of cnic
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%");
            });
        }

        $attempts = $query->get();

        // Create CSV content
        $csvData = [];
        $csvData[] = [
            'Student Name',
            'Email',
            'ID Type',
            'ID Number',
            'Contact',
            'Gender',
            'Qualification',
            'Test Title',
            'Score (%)',
            'Status',
            'Started At',
            'Completed At'
        ];

        foreach ($attempts as $attempt) {
            $csvData[] = [
                $attempt->student->full_name ?? 'N/A',
                $attempt->student->email ?? 'N/A',
                $attempt->student->id_type_label ?? 'N/A',  // FIXED: Use id_type_label
                $attempt->student->formatted_id ?? 'N/A',   // FIXED: Use formatted_id
                $attempt->student->contact_number ?? 'N/A',
                ucfirst($attempt->student->gender ?? 'N/A'),
                $attempt->student->qualification ?? 'N/A',
                $attempt->entryTest->title ?? 'N/A',
                number_format($attempt->percentage ?? 0, 2),
                $attempt->percentage >= ($attempt->entryTest->passing_score ?? 0) ? 'PASSED' : 'FAILED',
                $attempt->started_at ? $attempt->started_at->format('Y-m-d H:i:s') : 'N/A',
                $attempt->completed_at ? $attempt->completed_at->format('Y-m-d H:i:s') : 'N/A'
            ];
        }

        // Generate CSV file
        $filename = 'student_attempts_' . now()->format('Y_m_d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk allow retakes for multiple students
     */
    public function bulkAllowRetake(Request $request)
    {
        $request->validate([
            'attempt_ids' => 'required|array',
            'attempt_ids.*' => 'exists:entry_test_attempts,id'
        ]);

        $attempts = EntryTestAttempt::whereIn('id', $request->attempt_ids)
            ->with('student')
            ->get();

        $updatedCount = 0;
        foreach ($attempts as $attempt) {
            if ($attempt->student && !$attempt->student->is_retake_allowed) {
                $attempt->student->update(['is_retake_allowed' => true]);
                $updatedCount++;
            }
        }

        return back()->with('success', "Retake permission granted for {$updatedCount} students.");
    }

    /**
     * Get attempt statistics for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total_attempts' => EntryTestAttempt::count(),
            'completed_attempts' => EntryTestAttempt::where('status', 'completed')->count(),
            'passed_attempts' => EntryTestAttempt::completed()->passed()->count(),
            'failed_attempts' => EntryTestAttempt::completed()
                ->whereRaw('percentage < (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)')
                ->count(),
            'in_progress' => EntryTestAttempt::where('status', 'in_progress')->count(),
            'average_score' => round(EntryTestAttempt::completed()->avg('percentage') ?? 0, 2),
            'pass_rate' => EntryTestAttempt::completed()->count() > 0 
                ? round((EntryTestAttempt::completed()->passed()->count() / EntryTestAttempt::completed()->count()) * 100, 1)
                : 0
        ];

        return response()->json($stats);
    }
}