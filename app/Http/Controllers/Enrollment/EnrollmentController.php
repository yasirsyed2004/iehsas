<?php

namespace App\Http\Controllers\Enrollment;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\EnrollmentDocument;
use App\Http\Requests\EnrollmentVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EnrollmentController extends Controller
{
    /**
     * Show enrollment verification form
     */
    public function showVerificationForm()
    {
        return view('enrollment.verify');
    }

    /**
     * Verify enrollment code and redirect to document upload
     */
    public function verifyCode(EnrollmentVerificationRequest $request)
{
    // Get both clean and formatted versions of the ID
    $cleanIdNumber = $this->cleanIdNumber($request->id_number, $request->id_type);
    $originalIdNumber = $request->id_number;
    
    // For CNIC, also try the formatted version with dashes
    $formattedIdNumber = null;
    if ($request->id_type === 'cnic' && strlen($cleanIdNumber) === 13) {
        $formattedIdNumber = substr($cleanIdNumber, 0, 5) . '-' . 
                           substr($cleanIdNumber, 5, 7) . '-' . 
                           substr($cleanIdNumber, 12, 1);
    }
    
    // Search for student with multiple ID format possibilities
    $student = Student::where('id_type', $request->id_type)
                     ->where(function($query) use ($cleanIdNumber, $formattedIdNumber, $originalIdNumber) {
                         $query->where('id_number', $cleanIdNumber);
                         
                         if ($formattedIdNumber) {
                             $query->orWhere('id_number', $formattedIdNumber);
                         }
                         
                         // Also try the original input
                         $query->orWhere('id_number', $originalIdNumber);
                     })
                     ->first();

    if (!$student) {
        return back()->withErrors(['id_number' => 'No student found with this ID.']);
    }

    // Check if student is eligible for enrollment
    if (!$student->isEligibleForEnrollment()) {
        return back()->withErrors(['general' => 'You are not eligible for enrollment at this time.']);
    }

    // Verify the code
    if (!$student->verifyEnrollmentCode($request->verification_code)) {
        if ($student->enrollment_code_used) {
            return back()->withErrors(['verification_code' => 'This verification code has already been used.']);
        } elseif ($student->isEnrollmentCodeExpired()) {
            return back()->withErrors(['verification_code' => 'This verification code has expired. Please contact admissions for a new code.']);
        } else {
            return back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }
    }

    // Mark code as used and store student in session
    $student->markEnrollmentCodeAsUsed();
    Session::put('enrollment_student_id', $student->id);

    return redirect()->route('enrollment.documents')->with('success', 'Verification successful! You can now upload your documents.');
}

    /**
     * Show document upload form
     */
    public function showDocumentForm()
    {
        $studentId = Session::get('enrollment_student_id');
        
        if (!$studentId) {
            return redirect()->route('enrollment.verify')->withErrors(['general' => 'Please verify your enrollment code first.']);
        }

        $student = Student::findOrFail($studentId);
        
        // Check if enrollment is already submitted
        if ($student->enrollment_form_submitted) {
            return redirect()->route('enrollment.success')->with('info', 'Your enrollment has already been submitted.');
        }

        $documents = $student->enrollmentDocuments()->get()->keyBy('document_type');
        $documentTypes = EnrollmentDocument::getDocumentTypes();

        return view('enrollment.documents', compact('student', 'documents', 'documentTypes'));
    }

    /**
     * Submit enrollment form
     */
    public function submitEnrollment(Request $request)
    {
        $studentId = Session::get('enrollment_student_id');
        
        if (!$studentId) {
            return redirect()->route('enrollment.verify')->withErrors(['general' => 'Session expired. Please verify your enrollment code again.']);
        }

        $student = Student::findOrFail($studentId);

        // Check if all required documents are uploaded
        if (!$student->hasAllRequiredDocuments()) {
            return back()->withErrors(['general' => 'Please upload all required documents before submitting.']);
        }

        // Mark enrollment as submitted
        $student->markEnrollmentFormAsSubmitted();

        // Clear session
        Session::forget('enrollment_student_id');

        return redirect()->route('enrollment.success')->with('success', 'Your enrollment has been submitted successfully!');
    }

    /**
     * Show enrollment success page
     */
    public function showSuccess()
    {
        return view('enrollment.success');
    }

    /**
     * Get upload status for AJAX calls
     */
    public function getUploadStatus(Student $student)
    {
        $documents = $student->enrollmentDocuments;
        $documentTypes = array_keys(EnrollmentDocument::getDocumentTypes());
        
        $status = [];
        foreach ($documentTypes as $type) {
            $document = $documents->firstWhere('document_type', $type);
            $status[$type] = [
                'uploaded' => !is_null($document),
                'status' => $document ? $document->status : null,
                'filename' => $document ? $document->original_filename : null,
                'size' => $document ? $document->formatted_file_size : null,
            ];
        }

        return response()->json([
            'status' => $status,
            'all_uploaded' => $student->hasAllRequiredDocuments(),
            'can_submit' => $student->hasAllRequiredDocuments() && !$student->enrollment_form_submitted,
        ]);
    }

    /**
     * Clean ID number based on type
     */
    private function cleanIdNumber(string $idNumber, string $idType): string
    {
        if ($idType === 'cnic') {
            return preg_replace('/[^0-9]/', '', $idNumber);
        }
        
        return strtoupper(trim($idNumber));
    }
}