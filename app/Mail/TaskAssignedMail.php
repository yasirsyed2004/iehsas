<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Task $task;
    public User $user;
    public string $assignedByName;

    public function __construct(Task $task, User $user, string $assignedByName)
    {
        $this->task = $task;
        $this->user = $user;
        $this->assignedByName = $assignedByName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'IEHSAS - New Task Assigned: ' . $this->task->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-assigned',
            with: [
                'task' => $this->task,
                'user' => $this->user,
                'assignedByName' => $this->assignedByName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
