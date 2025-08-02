<?php

// File: app/Models/CourseLesson.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseLesson extends Model
{
    protected $fillable = [
        'course_module_id',
        'title',
        'description',
        'type',
        'content',
        'video_url',
        'file_path',
        'duration_minutes',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public function course()
    {
        return $this->module->course();
    }

    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class);
    }

    public function getUserProgress($user)
    {
        return $this->progress()->where('user_id', $user->id)->first();
    }

    public function isCompletedByUser($user)
    {
        return $this->progress()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
