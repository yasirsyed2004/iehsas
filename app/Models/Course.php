<?php
// File: app/Models/Course.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'category_id',
        'instructor_id',
        'thumbnail',
        'banner_image',
        'level',
        'status',
        'price',
        'discount_price',
        'duration_hours',
        'max_students',
        'requires_entry_test',
        'min_entry_test_score',
        'has_certificate',
        'prerequisites',
        'learning_outcomes',
        'requirements',
        'is_featured',
        'is_active',
        'published_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'min_entry_test_score' => 'decimal:2',
        'requires_entry_test' => 'boolean',
        'has_certificate' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'prerequisites' => 'array',
        'learning_outcomes' => 'array',
        'published_at' => 'datetime'
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(CourseLesson::class, CourseModule::class)
            ->orderBy('course_modules.order')
            ->orderBy('course_lessons.order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class)
            ->whereIn('status', ['enrolled', 'active']);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(CourseReview::class)->where('is_approved', true);
    }

    // Mutators
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // Accessors
    public function getEffectivePriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->discount_price || $this->price <= 0) {
            return 0;
        }
        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function getEnrollmentCountAttribute()
    {
        return $this->enrollments()->count();
    }

    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    public function getTotalLessonsAttribute()
    {
        return $this->lessons()->count();
    }

    public function getTotalModulesAttribute()
    {
        return $this->modules()->count();
    }

    public function getIsPublishedAttribute()
    {
        return $this->status === 'published' && $this->is_active;
    }

    public function getIsFreeAttribute()
    {
        return $this->effective_price <= 0;
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeFree($query)
    {
        return $query->where('price', 0)->whereNull('discount_price');
    }

    public function scopePaid($query)
    {
        return $query->where('price', '>', 0);
    }

    // Helper Methods
    public function canUserEnroll($user = null)
    {
        if (!$user) {
            return false;
        }

        // Check if already enrolled
        if ($this->enrollments()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // Check entry test requirement
        if ($this->requires_entry_test) {
            $hasPassedTest = $user->entryTestAttempts()
                ->where('status', 'completed')
                ->where('percentage', '>=', $this->min_entry_test_score ?? 60)
                ->exists();

            if (!$hasPassedTest) {
                return false;
            }
        }

        // Check enrollment limit
        if ($this->max_students && $this->enrollment_count >= $this->max_students) {
            return false;
        }

        return true;
    }

    public function getUserProgress($user)
    {
        if (!$user) {
            return 0;
        }

        $totalLessons = $this->total_lessons;
        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $this->id)
            ->where('is_completed', true)
            ->count();

        return round(($completedLessons / $totalLessons) * 100, 2);
    }

    // Route model binding
    public function getRouteKeyName()
    {
        return 'slug';
    }
}