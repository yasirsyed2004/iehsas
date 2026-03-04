<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskAuditLog;
use App\Models\TaskComment;

class TaskAuditService
{
    public static function logCreation(Task $task, string $performerType, int $performerId): void
    {
        TaskAuditLog::log(
            $task->id,
            'created',
            $performerType,
            $performerId,
            "Task '{$task->title}' was created"
        );
    }

    public static function logFieldChange(
        Task $task,
        string $field,
        ?string $oldValue,
        ?string $newValue,
        string $performerType,
        int $performerId
    ): void {
        if ($oldValue === $newValue) {
            return;
        }

        $fieldLabel = ucfirst(str_replace('_', ' ', $field));

        TaskAuditLog::log(
            $task->id,
            'field_updated',
            $performerType,
            $performerId,
            "{$fieldLabel} changed from '{$oldValue}' to '{$newValue}'",
            $field,
            $oldValue,
            $newValue
        );
    }

    public static function logStatusChange(
        Task $task,
        string $oldStatus,
        string $newStatus,
        string $performerType,
        int $performerId
    ): void {
        if ($oldStatus === $newStatus) {
            return;
        }

        $oldLabel = ucfirst(str_replace('_', ' ', $oldStatus));
        $newLabel = ucfirst(str_replace('_', ' ', $newStatus));

        TaskAuditLog::log(
            $task->id,
            'status_changed',
            $performerType,
            $performerId,
            "Status changed from '{$oldLabel}' to '{$newLabel}'",
            'status',
            $oldStatus,
            $newStatus
        );
    }

    public static function logComment(Task $task, TaskComment $comment, string $performerType, int $performerId): void
    {
        TaskAuditLog::log(
            $task->id,
            'comment_added',
            $performerType,
            $performerId,
            'A comment was added',
            null,
            null,
            null,
            ['comment_id' => $comment->id]
        );
    }

    public static function logProgressUpdate(Task $task, string $performerType, int $performerId, array $details = []): void
    {
        TaskAuditLog::log(
            $task->id,
            'progress_logged',
            $performerType,
            $performerId,
            "Daily progress update submitted" . (isset($details['progress_percentage']) ? " ({$details['progress_percentage']}%)" : ''),
            'progress_percentage',
            $details['old_progress'] ?? null,
            $details['progress_percentage'] ?? null,
            $details
        );
    }

    public static function logReview(Task $task, string $action, string $performerType, int $performerId, ?string $remarks = null): void
    {
        $actionLabel = ucfirst(str_replace('_', ' ', $action));

        TaskAuditLog::log(
            $task->id,
            'reviewed',
            $performerType,
            $performerId,
            "Manager review: {$actionLabel}" . ($remarks ? " - {$remarks}" : ''),
            null,
            null,
            null,
            ['review_action' => $action, 'remarks' => $remarks]
        );
    }

    public static function logClosed(Task $task, string $performerType, int $performerId): void
    {
        TaskAuditLog::log(
            $task->id,
            'closed',
            $performerType,
            $performerId,
            "Task was closed"
        );
    }

    public static function logReopened(Task $task, string $performerType, int $performerId): void
    {
        TaskAuditLog::log(
            $task->id,
            'reopened',
            $performerType,
            $performerId,
            "Task was reopened for revision"
        );
    }
}
