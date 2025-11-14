<?php

namespace App\Services;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class EnrollmentPdfService
{
    /**
     * Generate enrollment PDF for a student
     */
    public function generateEnrollmentPDF(Student $student)
    {
        // Load all necessary relationships
        $student->load([
            'latestAttempt.entryTest',
            'enrollmentDocuments',
            'selectedCourses.course'
        ]);

        // Get grouped documents
        $groupedDocuments = $student->getGroupedDocuments();
        
        // Filter valid courses
        $validCourses = $student->selectedCourses->filter(fn($s) => $s->course !== null);

        // Prepare data for PDF
        $data = [
            'student' => $student,
            'attempt' => $student->latestAttempt,
            'courses' => $validCourses,
            'documents' => $groupedDocuments,
            'generatedAt' => now(),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('admin.enrollments.pdf', $data);
        
        // Set paper configuration
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Download enrollment PDF
     */
    public function downloadPDF(Student $student)
    {
        $pdf = $this->generateEnrollmentPDF($student);
        
        $filename = 'enrollment_' . $student->id . '_' . $student->full_name . '_' . now()->format('Ymd_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Stream enrollment PDF (view in browser)
     */
    public function streamPDF(Student $student)
    {
        $pdf = $this->generateEnrollmentPDF($student);
        
        return $pdf->stream('enrollment_' . $student->id . '.pdf');
    }
}