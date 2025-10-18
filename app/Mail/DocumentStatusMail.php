<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Student $student;
    public string $status;
    public ?string $message;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, string $status, ?string $message = null)
    {
        $this->student = $student;
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusLabels = [
            'approved' => 'Documents Approved',
            'rejected' => 'Documents Need Revision',
            'pending' => 'Documents Under Review',
        ];

        $subject = 'IEHSAS Enrollment - ' . ($statusLabels[$this->status] ?? 'Document Status Update');

        return new Envelope(
            from: config('mail.enrollment.from.address'),
            replyTo: config('mail.enrollment.reply_to'),
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.document-status',
            with: [
                'student' => $this->student,
                'status' => $this->status,
                'message' => $this->message,
                'enrollmentUrl' => route('enrollment.documents'),
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