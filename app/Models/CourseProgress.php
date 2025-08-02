<?php
// File: app/Models/CourseProgress.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseProgress extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'course_lesson_id',
        'is_completed',
        'watch_time_seconds',
        'completion_percentage',
        'started_at',
        'completed_at',
        'quiz_answers',
        'quiz_score'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completion_percentage' => 'decimal:2',
        'quiz_score' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'quiz_answers' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    public function markAsCompleted()
    {
        $this->update([
            'is_completed' => true,
            'completion_percentage' => 100,
            'completed_at' => now(),
            'started_at' => $this->started_at ?? now()
        ]);

        // Update enrollment progress
        $enrollment = CourseEnrollment::where('user_id', $this->user_id)
            ->where('course_id', $this->course_id)
            ->first();

        if ($enrollment) {
            $enrollment->updateProgress();
        }
    }
}