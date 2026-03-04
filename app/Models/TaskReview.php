<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskReview extends Model
{
    protected $fillable = [
        'task_id',
        'admin_id',
        'action',
        'remarks',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'remark' => 'Remark',
            'revision_requested' => 'Revision Requested',
            'approved' => 'Approved',
            'closed' => 'Closed',
            default => ucfirst($this->action),
        };
    }

    public function getActionBadgeClassAttribute(): string
    {
        return match($this->action) {
            'remark' => 'info',
            'revision_requested' => 'warning',
            'approved' => 'success',
            'closed' => 'dark',
            default => 'secondary',
        };
    }

    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'remark' => 'fa-comment text-info',
            'revision_requested' => 'fa-undo text-warning',
            'approved' => 'fa-check-circle text-success',
            'closed' => 'fa-lock text-dark',
            default => 'fa-circle text-secondary',
        };
    }
}
