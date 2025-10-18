<?php

namespace App\Services;

use App\Models\Student;
use App\Mail\EnrollmentVerificationMail;
use App\Mail\DocumentStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class EnrollmentMailService
{
    /**
     * Send enrollment verification email to student
     */
    public function sendEnrollmentVerification(Student $student): bool
    {
        try {
            // Generate verification code
            $student->generateEnrollmentCode();
            
            // Send email
            Mail::to($student->email)
                ->send(new EnrollmentVerificationMail($student));
            
            Log::info("Enrollment verification email sent to student", [
                'student_id' => $student->id,
                'email' => $student->email,
                'verification_code' => $student->enrollment_verification_code
            ]);
            
            return true;
            
        } catch (Exception $e) {
            Log::error("Failed to send enrollment verification email", [
                'student_id' => $student->id,
                'email' => $student->email,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Send document status notification to student
     */
    public function sendDocumentStatusNotification(Student $student, string $status, string $message = null): bool
    {
        try {
            Mail::to($student->email)
                ->send(new DocumentStatusMail($student, $status, $message));
            
            Log::info("Document status email sent to student", [
                'student_id' => $student->id,
                'email' => $student->email,
                'status' => $status
            ]);
            
            return true;
            
        } catch (Exception $e) {
            Log::error("Failed to send document status email", [
                'student_id' => $student->id,
                'email' => $student->email,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Test email configuration
     */
    public function testEmailConfiguration(string $testEmail): array
    {
        try {
            Mail::raw('This is a test email from IEHSAS Enrollment System.', function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('IEHSAS Enrollment System - Test Email');
            });
            
            return [
                'success' => true,
                'message' => 'Test email sent successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get email configuration status
     */
    public function getConfigurationStatus(): array
    {
        $config = config('mail');
        $enrollmentConfig = config('mail.enrollment');
        
        $status = [
            'mailer' => $config['default'],
            'host' => $config['mailers'][$config['default']]['host'] ?? 'Not configured',
            'port' => $config['mailers'][$config['default']]['port'] ?? 'Not configured',
            'encryption' => $config['mailers'][$config['default']]['encryption'] ?? 'None',
            'from_address' => $enrollmentConfig['from']['address'],
            'from_name' => $enrollmentConfig['from']['name'],
            'reply_to' => $enrollmentConfig['reply_to'],
            'code_expiry_hours' => $enrollmentConfig['verification_code_expiry_hours'],
        ];
        
        // Check if critical settings are configured
        $isConfigured = !empty(config('mail.mailers.smtp.username')) && 
                       !empty(config('mail.mailers.smtp.password'));
        
        $status['is_configured'] = $isConfigured;
        
        return $status;
    }
}