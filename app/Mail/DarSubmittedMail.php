<?php

namespace App\Mail;

use App\Models\DailyActivityReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DarSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public DailyActivityReport $dar;

    public function __construct(DailyActivityReport $dar)
    {
        $this->dar = $dar;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'IEHSAS - DAR Submitted: ' . $this->dar->user->name . ' (' . $this->dar->report_date->format('M d, Y') . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dar-submitted',
            with: ['dar' => $this->dar],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
