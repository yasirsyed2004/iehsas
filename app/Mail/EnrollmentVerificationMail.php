<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Student $student;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.enrollment.from.address'),
            replyTo: config('mail.enrollment.reply_to'),
            subject: 'IEHSAS Enrollment Form - Verification Required',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.enrollment-verification',
            with: [
                'student' => $this->student,
                'verificationUrl' => route('enrollment.verify'),
                'expiryHours' => config('mail.enrollment.verification_code_expiry_hours', 48),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}