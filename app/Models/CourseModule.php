<?php
// File: app/Models/CourseModule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseModule extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'duration_minutes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class)->orderBy('order');
    }

    public function activeLessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class)->where('is_active', true)->orderBy('order');
    }

    public function getLessonsCountAttribute()
    {
        return $this->lessons()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}