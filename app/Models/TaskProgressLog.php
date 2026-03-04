<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskProgressLog extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'log_date',
        'progress_percentage',
        'description',
        'is_locked',
        'submitted_at',
    ];

    protected $casts = [
        'log_date' => 'date',
        'is_locked' => 'boolean',
        'submitted_at' => 'datetime',
        'progress_percentage' => 'integer',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ProgressLogAttachment::class, 'progress_log_id');
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('log_date', $date);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    // Methods
    public function lock(): void
    {
        $this->is_locked = true;
        $this->submitted_at = now();
        $this->save();

        // Update task progress percentage
        $this->task->update(['progress_percentage' => $this->progress_percentage]);
    }

    public function isEditable(): bool
    {
        return !$this->is_locked;
    }
}
