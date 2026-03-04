<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskAuditLog extends Model
{
    protected $fillable = [
        'task_id',
        'action',
        'field_name',
        'old_value',
        'new_value',
        'performed_by_type',
        'performed_by_id',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function getPerformerNameAttribute(): string
    {
        if ($this->performed_by_type === 'admin') {
            return Admin::find($this->performed_by_id)?->name ?? 'Admin';
        }
        return User::find($this->performed_by_id)?->name ?? 'User';
    }

    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'created' => 'fa-plus-circle text-success',
            'status_changed' => 'fa-exchange-alt text-info',
            'field_updated' => 'fa-edit text-primary',
            'comment_added' => 'fa-comment text-info',
            'progress_logged' => 'fa-chart-line text-success',
            'reviewed' => 'fa-check-double text-primary',
            'closed' => 'fa-lock text-danger',
            'reopened' => 'fa-unlock text-warning',
            'attachment_added' => 'fa-paperclip text-secondary',
            default => 'fa-circle text-muted',
        };
    }

    public static function log(
        int $taskId,
        string $action,
        string $performerType,
        int $performerId,
        ?string $description = null,
        ?string $fieldName = null,
        ?string $oldValue = null,
        ?string $newValue = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'task_id' => $taskId,
            'action' => $action,
            'field_name' => $fieldName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'performed_by_type' => $performerType,
            'performed_by_id' => $performerId,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
