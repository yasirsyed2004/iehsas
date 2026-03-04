<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'expected_deliverables',
        'department_id',
        'assigned_to',
        'assigned_by',
        'priority',
        'status',
        'deadline',
        'start_date',
        'progress_percentage',
        'is_external_shared',
        'external_share_token',
        'is_locked',
        'completed_at',
        'closed_at',
        'closed_by',
        'submitted_for_review_at',
    ];

    protected $casts = [
        'deadline' => 'date',
        'start_date' => 'date',
        'completed_at' => 'datetime',
        'closed_at' => 'datetime',
        'submitted_for_review_at' => 'datetime',
        'is_external_shared' => 'boolean',
        'is_locked' => 'boolean',
        'progress_percentage' => 'integer',
    ];

    // Relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_by');
    }

    public function closedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'closed_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->orderBy('created_at', 'desc');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(TaskAuditLog::class)->orderBy('created_at', 'desc');
    }

    public function progressLogs(): HasMany
    {
        return $this->hasMany(TaskProgressLog::class)->orderBy('log_date', 'desc');
    }

    public function latestProgressLog(): HasOne
    {
        return $this->hasOne(TaskProgressLog::class)->latestOfMany('log_date');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TaskReview::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeNotStarted($query)
    {
        return $query->where('status', 'not_started');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeSubmittedForReview($query)
    {
        return $query->where('status', 'submitted_for_review');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled', 'closed']);
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->whereBetween('deadline', [now(), now()->addDays($days)])
            ->whereNotIn('status', ['completed', 'cancelled', 'closed']);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors
    public function getIsOverdueAttribute()
    {
        return $this->deadline && $this->deadline < now() && !in_array($this->status, ['completed', 'cancelled', 'closed']);
    }

    public function getDaysUntilDeadlineAttribute()
    {
        if (!$this->deadline) {
            return null;
        }
        return now()->diffInDays($this->deadline, false);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'not_started' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'on_hold' => 'secondary',
            'submitted_for_review' => 'primary',
            'closed' => 'dark',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getPriorityBadgeClassAttribute()
    {
        return match($this->priority) {
            'low' => 'secondary',
            'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary',
        };
    }

    // Editability checks
    public function isEditable(): bool
    {
        return !in_array($this->status, ['closed', 'cancelled']) && !$this->is_locked;
    }

    public function isEditableByEmployee(): bool
    {
        return !in_array($this->status, ['closed', 'cancelled', 'submitted_for_review']) && !$this->is_locked;
    }

    public function canSubmitForReview(): bool
    {
        return in_array($this->status, ['in_progress', 'completed']) && !$this->is_locked;
    }

    public function canBeReviewedByManager(): bool
    {
        return in_array($this->status, ['submitted_for_review', 'in_progress', 'completed']);
    }

    // Action methods
    public function submitForReview(): void
    {
        $this->status = 'submitted_for_review';
        $this->submitted_for_review_at = now();
        $this->save();
    }

    public function close(int $adminId): void
    {
        $this->status = 'closed';
        $this->closed_at = now();
        $this->closed_by = $adminId;
        $this->is_locked = true;
        $this->save();
    }

    public function requestRevision(): void
    {
        $this->status = 'in_progress';
        $this->submitted_for_review_at = null;
        $this->save();
    }

    public function approve(): void
    {
        $this->status = 'completed';
        $this->progress_percentage = 100;
        $this->completed_at = now();
        $this->save();
    }

    // Helper Methods
    public function generateShareToken()
    {
        $this->external_share_token = Str::random(64);
        $this->is_external_shared = true;
        $this->save();
        return $this->external_share_token;
    }

    public function revokeShareToken()
    {
        $this->external_share_token = null;
        $this->is_external_shared = false;
        $this->save();
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->progress_percentage = 100;
        $this->completed_at = now();
        $this->save();
    }

    // Static helpers
    public static function getStatuses()
    {
        return [
            'not_started' => 'Not Started',
            'in_progress' => 'In Progress',
            'on_hold' => 'On Hold',
            'completed' => 'Completed',
            'submitted_for_review' => 'Submitted for Review',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled',
        ];
    }

    public static function getPriorities()
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }
}
