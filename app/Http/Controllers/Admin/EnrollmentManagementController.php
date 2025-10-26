<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\EnrollmentDocument;
use App\Services\EnrollmentMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentManagementController extends Controller
{
    protected $mailService;

    public function __construct(EnrollmentMailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Display enrollments dashboard
     */
    public function index(Request $request)
    {
        $query = Student::with(['latestAttempt', 'enrollmentDocuments']);

        // Filter by enrollment status
        if ($request->filled('status')) {
            $status = $request->status;
            
            $query->where(function($q) use ($status) {
                switch ($status) {
                    case 'eligible':
                        $q->whereHas('attempts', function($attempt) {
                            $attempt->where('status', 'pass');
                        })->where('enrollment_form_submitted', false)
                          ->whereNull('enrollment_verification_code');
                        break;
                        
                    case 'code_sent':
                        $q->whereNotNull('enrollment_verification_code')
                          ->where('enrollment_code_used', false)
                          ->where('enrollment_form_submitted', false);
                        break;
                        
                    case 'in_progress':
                        $q->where('enrollment_code_used', true)
                          ->where('enrollment_form_submitted', false);
                        break;
                        
                    case 'completed':
                        $q->where('enrollment_form_submitted', true);
                        break;
                }
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get summary statistics
        $stats = [
            'eligible' => Student::eligibleForEnrollment()->count(),
            'code_sent' => Student::whereNotNull('enrollment_verification_code')
                                 ->where('enrollment_code_used', false)
                                 ->where('enrollment_form_submitted', false)->count(),
            'in_progress' => Student::where('enrollment_code_used', true)
                                   ->where('enrollment_form_submitted', false)->count(),
            'completed' => Student::enrollmentCompleted()->count(),
        ];

        return view('admin.enrollments.index', compact('students', 'stats'));
    }

    /**
     * Show individual student enrollment details
     */
    public function show(Student $student)
    {
        $student->load(['latestAttempt', 'enrollmentDocuments', 'selectedCourses']);
        
        return view('admin.enrollments.show', compact('student'));
    }

    /**
     * Send enrollment form to a student
     */
    public function sendEnrollmentForm(Student $student)
    {
        // Check if student is eligible
        if (!$student->hasPassedEntryTest()) {
            return back()->withErrors(['error' => 'Student has not passed the entry test.']);
        }

        if ($student->enrollment_form_submitted) {
            return back()->withErrors(['error' => 'Student has already completed enrollment.']);
        }

        // Send email
        $success = $this->mailService->sendEnrollmentVerification($student);

        if ($success) {
            return back()->with('success', 'Enrollment form sent successfully to ' . $student->email);
        } else {
            return back()->withErrors(['error' => 'Failed to send enrollment form. Please check email configuration.']);
        }
    }

    /**
     * Send enrollment form from student attempts page
     */
    public function sendEnrollmentFromAttempts(Student $student)
    {
        return $this->sendEnrollmentForm($student);
    }

    /**
     * Approve a document
     */
    public function approveDocument(EnrollmentDocument $document, Request $request)
    {
        $document->update([
            'status' => EnrollmentDocument::STATUS_APPROVED,
            'admin_comments' => $request->input('comments'),
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);

        // Check if all documents are approved
        if ($document->student->areAllDocumentsApproved()) {
            $this->mailService->sendDocumentStatusNotification(
                $document->student,
                'approved',
                'Congratulations! All your documents have been approved. Your enrollment is now complete.'
            );
        }

        return back()->with('success', 'Document approved successfully.');
    }

    /**
     * Reject a document
     */
    public function rejectDocument(EnrollmentDocument $document, Request $request)
    {
        $request->validate([
            'comments' => 'required|string|max:1000'
        ]);

        $document->update([
            'status' => EnrollmentDocument::STATUS_REJECTED,
            'admin_comments' => $request->input('comments'),
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);

        // Send rejection notification
        $this->mailService->sendDocumentStatusNotification(
            $document->student,
            'rejected',
            $request->input('comments')
        );

        return back()->with('success', 'Document rejected and student notified.');
    }

    /**
     * Bulk send enrollment forms
     */
    public function bulkSendForms(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        $students = Student::whereIn('id', $request->student_ids)->get();
        $sent = 0;
        $failed = 0;

        foreach ($students as $student) {
            if ($student->hasPassedEntryTest() && !$student->enrollment_form_submitted) {
                if ($this->mailService->sendEnrollmentVerification($student)) {
                    $sent++;
                } else {
                    $failed++;
                }
            }
        }

        return back()->with('success', "Enrollment forms sent to {$sent} students." . 
                           ($failed > 0 ? " {$failed} failed to send." : ""));
    }

    /**
     * Bulk approve documents
     */
    public function bulkApproveDocuments(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:enrollment_documents,id'
        ]);

        $documents = EnrollmentDocument::whereIn('id', $request->document_ids)->get();
        $approved = 0;

        foreach ($documents as $document) {
            if ($document->status === EnrollmentDocument::STATUS_PENDING) {
                $document->update([
                    'status' => EnrollmentDocument::STATUS_APPROVED,
                    'reviewed_at' => now(),
                    'reviewed_by' => Auth::id(),
                ]);
                $approved++;
            }
        }

        return back()->with('success', "{$approved} documents approved successfully.");
    }

    /**
     * Show email test form
     */
    public function showEmailTest()
    {
        $config = $this->mailService->getConfigurationStatus();
        
        return view('admin.enrollments.test-email', compact('config'));
    }

    /**
     * Send test email
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        $result = $this->mailService->testEmailConfiguration($request->test_email);

        if ($result['success']) {
            return back()->with('success', 'Test email sent successfully to ' . $request->test_email);
        } else {
            return back()->withErrors(['error' => $result['message']]);
        }
    }
}