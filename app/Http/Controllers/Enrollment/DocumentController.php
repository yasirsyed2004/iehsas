<?php

namespace App\Http\Controllers\Enrollment;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\EnrollmentDocument;
use App\Http\Requests\DocumentUploadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentController extends Controller
{
    /**
     * Upload a document
     */
    public function upload(DocumentUploadRequest $request)
    {
        $studentId = Session::get('enrollment_student_id');
        
        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please verify your enrollment code again.'
            ], 401);
        }

        $student = Student::findOrFail($studentId);
        
        if ($student->enrollment_form_submitted) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment has already been submitted.'
            ], 400);
        }

        $file = $request->file('document');
        $documentType = $request->document_type;

        try {
            // Generate unique filename
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $storedName = $this->generateStoredFilename($student, $documentType, $extension);
            
            // Create directory structure
            $directory = $this->getStudentDirectory($student);
            $filePath = $directory . '/' . $storedName;

            // Store file
            $file->storeAs('', $filePath, 'enrollment');

            // Delete existing document of same type
            $existingDocument = $student->enrollmentDocuments()
                                      ->where('document_type', $documentType)
                                      ->first();
            
            if ($existingDocument) {
                $existingDocument->deleteFile();
                $existingDocument->delete();
            }

            // Create document record
            $document = EnrollmentDocument::create([
                'student_id' => $student->id,
                'document_type' => $documentType,
                'original_filename' => $originalName,
                'stored_filename' => $storedName,
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'status' => EnrollmentDocument::STATUS_PENDING,
                'uploaded_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully.',
                'document' => [
                    'id' => $document->id,
                    'type' => $document->document_type,
                    'filename' => $document->original_filename,
                    'size' => $document->formatted_file_size,
                    'status' => $document->status_label,
                    'view_url' => $document->file_url,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a document
     */
    public function delete(EnrollmentDocument $document)
    {
        $studentId = Session::get('enrollment_student_id');
        
        if (!$studentId || $document->student_id != $studentId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        try {
            $document->deleteFile();
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View a document
     */
    public function view(EnrollmentDocument $document)
    {
        // Check authorization
        if (!$this->canAccessDocument($document)) {
            abort(403, 'Unauthorized access to document.');
        }

        if (!$document->fileExists()) {
            abort(404, 'Document file not found.');
        }

        $fileContents = $document->getFileContents();
        
        return response($fileContents)
            ->header('Content-Type', $document->mime_type)
            ->header('Content-Disposition', 'inline; filename="' . $document->original_filename . '"');
    }

    /**
     * Download a document
     */
    public function download(EnrollmentDocument $document)
    {
        // Check authorization
        if (!$this->canAccessDocument($document)) {
            abort(403, 'Unauthorized access to document.');
        }

        if (!$document->fileExists()) {
            abort(404, 'Document file not found.');
        }

        return Storage::disk('enrollment')->download($document->file_path, $document->original_filename);
    }

    /**
     * Validate document before upload (AJAX)
     */
    public function validateDocument(Request $request)
    {
        $request->validate([
            'document' => 'required|file',
            'document_type' => 'required|in:identity,education,cv,photo'
        ]);

        $file = $request->file('document');
        $documentType = $request->document_type;

        // Validate file type based on document type
        $allowedTypes = $this->getAllowedMimeTypes($documentType);
        $maxSize = $this->getMaxFileSize($documentType);

        $errors = [];

        // Check file type
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            $errors[] = 'Invalid file type for ' . $documentType . ' document.';
        }

        // Check file size
        if ($file->getSize() > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size of ' . number_format($maxSize / 1024 / 1024, 1) . 'MB.';
        }

        return response()->json([
            'valid' => empty($errors),
            'errors' => $errors,
            'file_info' => [
                'name' => $file->getClientOriginalName(),
                'size' => number_format($file->getSize() / 1024, 1) . ' KB',
                'type' => $file->getMimeType()
            ]
        ]);
    }

    /**
     * Check if user can access document
     */
    private function canAccessDocument(EnrollmentDocument $document): bool
    {
        // Check if it's the student's own document
        $studentId = Session::get('enrollment_student_id');
        if ($studentId && $document->student_id == $studentId) {
            return true;
        }

        // Check if it's an admin user
        if (auth()->check()) {
            return true;
        }

        return false;
    }

    /**
     * Generate unique stored filename
     */
    private function generateStoredFilename(Student $student, string $documentType, string $extension): string
    {
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);
        
        return "{$documentType}_{$student->id}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get student directory path
     */
    private function getStudentDirectory(Student $student): string
    {
        return "student_{$student->id}";
    }

    /**
     * Get allowed MIME types for document type
     */
    private function getAllowedMimeTypes(string $documentType): array
    {
        $imageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $documentTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        return match($documentType) {
            'photo' => $imageTypes,
            'identity', 'education', 'cv' => array_merge($imageTypes, $documentTypes),
            default => []
        };
    }

    /**
     * Get maximum file size for document type
     */
    private function getMaxFileSize(string $documentType): int
    {
        // Default 5MB, but can be customized per document type
        $defaultSize = 5 * 1024 * 1024; // 5MB in bytes
        
        return match($documentType) {
            'photo' => 2 * 1024 * 1024, // 2MB for photos
            default => $defaultSize
        };
    }
}