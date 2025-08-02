<?php
// File: app/Models/CourseEnrollment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'entry_test_attempt_id',
        'status',
        'enrolled_at',
        'started_at',
        'completed_at',
        'progress_percentage'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress_percentage' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function entryTestAttempt(): BelongsTo
    {
        return $this->belongsTo(EntryTestAttempt::class);
    }

    public function updateProgress()
    {
        $totalLessons = $this->course->total_lessons;
        if ($totalLessons === 0) {
            return;
        }

        $completedLessons = CourseProgress::where('user_id', $this->user_id)
            ->where('course_id', $this->course_id)
            ->where('is_completed', true)
            ->count();

        $progress = round(($completedLessons / $totalLessons) * 100, 2);
        
        $this->update([
            'progress_percentage' => $progress,
            'started_at' => $this->started_at ?? now(),
            'status' => $progress >= 100 ? 'completed' : 'active',
            'completed_at' => $progress >= 100 ? now() : null
        ]);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['enrolled', 'active']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}