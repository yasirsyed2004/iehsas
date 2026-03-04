<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTaskDeadlineReminders extends Command
{
    protected $signature = 'tasks:send-deadline-reminders';
    protected $description = 'Send email/notification reminders for tasks with upcoming deadlines (within 2 days)';

    public function handle(): int
    {
        $tasks = Task::whereNotNull('deadline')
            ->whereNotIn('status', ['completed', 'closed', 'cancelled'])
            ->whereDate('deadline', '<=', now()->addDays(2))
            ->whereDate('deadline', '>=', now())
            ->with(['assignedUser', 'department'])
            ->get();

        if ($tasks->isEmpty()) {
            $this->info('No upcoming deadlines to remind about.');
            return 0;
        }

        $count = 0;
        foreach ($tasks as $task) {
            if (!$task->assigned_to || !$task->assignedUser) {
                continue;
            }

            $daysLeft = now()->diffInDays($task->deadline, false);
            $urgency = $daysLeft <= 0 ? 'due today' : "due in {$daysLeft} day(s)";

            Notification::createForUser(
                $task->assigned_to,
                'deadline_reminder',
                'Deadline Reminder',
                "Task \"{$task->title}\" is {$urgency}. Please ensure timely completion.",
                ['task_id' => $task->id]
            );

            $count++;
        }

        $this->info("Sent {$count} deadline reminder(s).");
        return 0;
    }
}
