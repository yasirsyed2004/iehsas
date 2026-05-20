<?php
// File: app/Http/Controllers/EntryTest/StudentRegistrationController.php

namespace App\Http\Controllers\EntryTest;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRegistrationRequest;
use App\Models\Student;
use App\Models\EntryTest;
use App\Models\EnrollmentDocument;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class StudentRegistrationController extends Controller
{
    /**
     * Show the registration form
     */
    public function showForm()
    {
        $entryTest = EntryTest::where('is_active', true)->first();
        
        if (!$entryTest) {
            return redirect()->route('home')
                ->with('error', 'No active entry test available.');
        }

        // Get published and active courses, sorted alphabetically
        $courses = Course::where('status', 'published')
            ->where('is_active', true)
            ->orderBy('title', 'asc')
            ->get(['id', 'title', 'level']);

        // Pass additional data needed for the enhanced form
        $nationalities = config('nationalities');
        
        $idTypes = [
            'cnic' => 'CNIC',
            'passport' => 'Passport',
            'driving_license' => 'Driving License'
        ];

        return view('entry-test.register', compact('entryTest', 'nationalities', 'idTypes', 'courses'));
    }

    /**
     * Store the registration data
     */
    public function store(StudentRegistrationRequest $request)
    {
        // Get cleaned and validated data from Form Request
        $validatedData = $request->getCleanedData();

        // Extract course IDs for later use
        $selectedCourseIds = $validatedData['courses'];
        unset($validatedData['courses']); // Remove from array before creating student
        
        // Check if student already exists with this ID combination
        $existingStudent = Student::where('id_type', $validatedData['id_type'])
                                 ->where('id_number', $validatedData['id_number'])
                                 ->first();
        
        if ($existingStudent) {
            // Student exists - check if they can attempt test again
            if (!$existingStudent->canAttemptTest()) {
                return back()->withErrors([
                    'id_number' => 'You have already attempted the test with this ' . 
                                  $existingStudent->id_type_label . 
                                  '. Contact admin for retake permission.'
                ])->withInput();
            }
            
            // Student can retake - update their information
            // Student can retake - update their information
            $student = $this->updateExistingStudent($existingStudent, $validatedData);

            // Update course selections
            $student->selectedCourses()->sync($selectedCourseIds);

            $message = 'Registration updated successfully! You are allowed to retake the test.';
        } else {
            // New student - create record
            $student = $this->createNewStudent($validatedData);

            // Attach selected courses
            $student->selectedCourses()->attach($selectedCourseIds, [
                'selected_at' => now()
            ]);

            $message = 'Registration successful! Please review the test instructions.';
        }

        // Handle eligibility proof upload (proof_type is required for multi-option
        // combinations and resolved automatically for single-option ones)
        if ($request->hasFile('eligibility_proof')) {
            $this->storeEligibilityProof(
                $student,
                $request->file('eligibility_proof'),
                $request->input('eligibility_proof_type')
            );
        }
        
        // Store student ID in session for test flow
        session(['registered_student_id' => $student->id]);
        
        // Get the active entry test for redirection
        $entryTest = EntryTest::where('is_active', true)->first();
        
        return redirect()->route('entry-test.instructions', $entryTest->id)
            ->with('success', $message);
    }

    /**
     * Create a new student record
     */
    protected function createNewStudent(array $data): Student
    {
        return Student::create([
            'full_name' => $data['full_name'],
            'father_name' => $data['father_name'],
            'email' => $data['email'],
            'contact_number' => $data['contact_number'],
            'date_of_birth' => $data['date_of_birth'],
            'id_type' => $data['id_type'],
            'id_number' => $data['id_number'],
            'nationality' => $data['nationality'],
            'gender' => $data['gender'],
            'qualification' => $data['qualification'],
            'qualification_other' => $data['qualification_other'] ?? null,
            'session_mode' => $data['session_mode'],
            'home_address' => $data['home_address'],
            'is_retake_allowed' => false,
        ]);
    }

    /**
     * Update existing student record (for retakes)
     */
    protected function updateExistingStudent(Student $student, array $data): Student
    {
        $student->update([
            'full_name' => $data['full_name'],
            'father_name' => $data['father_name'],
            'email' => $data['email'],
            'contact_number' => $data['contact_number'],
            'date_of_birth' => $data['date_of_birth'],
            'nationality' => $data['nationality'],
            'gender' => $data['gender'],
            'qualification' => $data['qualification'],
            'qualification_other' => $data['qualification_other'] ?? null,
            'session_mode' => $data['session_mode'],
            'home_address' => $data['home_address'],
            // Note: We don't update id_type, id_number, or is_retake_allowed
        ]);

        return $student->fresh();
    }

    /**
     * Store eligibility proof document.
     *
     * Determines the correct document_type + sub_type based on the student's
     * qualification + session_mode and the user-selected proof type (when
     * multiple options are offered).
     */
    protected function storeEligibilityProof(Student $student, UploadedFile $file, ?string $selectedProofKey = null): void
    {
        $acceptedKeys = $student->getAcceptedProofKeys();
        if (empty($acceptedKeys)) {
            return;
        }

        // Resolve which proof key applies for this upload
        if (count($acceptedKeys) === 1) {
            $proofKey = $acceptedKeys[0];
        } else {
            // Multi-option (Physical + Intermediate/Other) — user must specify
            if (!$selectedProofKey || !in_array($selectedProofKey, $acceptedKeys, true)) {
                return;
            }
            $proofKey = $selectedProofKey;
        }

        $option = Student::PROOF_OPTIONS[$proofKey];
        $documentType = $option['document_type'];

        $storedFilename = 'eligibility_' . $student->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs(
            'eligibility-proofs/' . $student->id,
            $storedFilename,
            'enrollment'
        );

        // Delete existing proof of same sub_type if re-registering
        $existing = $student->enrollmentDocuments()
            ->where('document_type', $documentType)
            ->where('sub_type', $proofKey)
            ->first();

        if ($existing) {
            \Illuminate\Support\Facades\Storage::disk('enrollment')->delete($existing->file_path);
            $existing->delete();
        }

        EnrollmentDocument::create([
            'student_id' => $student->id,
            'document_type' => $documentType,
            'sub_type' => $proofKey,
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $storedFilename,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);
    }

    /**
     * Helper method to format ID number based on type
     * (This is also available in the Student model, but kept here for direct use)
     */
    protected function formatIdNumber(string $type, string $number): string
    {
        $cleaned = preg_replace('/[^A-Z0-9]/i', '', $number);
        
        if ($type === 'cnic' && strlen($cleaned) === 13) {
            return substr($cleaned, 0, 5) . '-' . 
                   substr($cleaned, 5, 7) . '-' . 
                   substr($cleaned, 12, 1);
        }
        
        return strtoupper($cleaned);
    }

    /**
     * AJAX endpoint to validate ID format (optional - for real-time validation)
     */
    public function validateId(Request $request)
    {
        $request->validate([
            'id_type' => 'required|in:cnic,passport,driving_license',
            'id_number' => 'required|string'
        ]);

        $idType = $request->id_type;
        $idNumber = $this->formatIdNumber($idType, $request->id_number);

        // Check format
        $isValidFormat = Student::validateIdFormat($idType, $idNumber);
        
        if (!$isValidFormat) {
            return response()->json([
                'valid' => false,
                'message' => $this->getFormatMessage($idType),
                'formatted' => $idNumber
            ]);
        }

        // Check for duplicates
        $existingStudent = Student::where('id_type', $idType)
                                 ->where('id_number', $idNumber)
                                 ->first();

        if ($existingStudent && !$existingStudent->canAttemptTest()) {
            return response()->json([
                'valid' => false,
                'message' => 'This ' . $existingStudent->id_type_label . ' has already been used for test registration.',
                'formatted' => $idNumber
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Valid ' . $this->getIdTypeLabel($idType),
            'formatted' => $idNumber
        ]);
    }

    /**
     * Get format guidance message for ID types
     */
    protected function getFormatMessage(string $idType): string
    {
        return match($idType) {
            'cnic' => 'Format: 12345-1234567-1 (13 digits with dashes)',
            'passport' => 'Format: AB1234567 (2 letters + 7 digits)',
            'driving_license' => 'Format: ABC12345 (8-15 alphanumeric characters)',
            default => 'Invalid format'
        };
    }

    /**
     * Get ID type label
     */
    protected function getIdTypeLabel(string $idType): string
    {
        return match($idType) {
            'cnic' => 'CNIC',
            'passport' => 'Passport',
            'driving_license' => 'Driving License',
            default => 'ID'
        };
    }

    /**
     * Show registration success page (optional)
     */
    public function success()
    {
        $studentId = session('registered_student_id');
        
        if (!$studentId) {
            return redirect()->route('entry-test.register')
                ->with('error', 'No registration found.');
        }

        $student = Student::findOrFail($studentId);
        
        return view('entry-test.registration-success', compact('student'));
    }

    /**
     * Clear registration session (if needed)
     */
    public function clearSession()
    {
        session()->forget('registered_student_id');
        
        return redirect()->route('entry-test.index')
            ->with('info', 'Registration session cleared.');
    }
}