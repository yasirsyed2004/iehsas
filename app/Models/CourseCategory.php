<?php
// File: app/Models/CourseCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CourseCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    public function activeCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id')
            ->where('is_active', true)
            ->where('status', 'published');
    }

    // Mutators
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // Accessors
    public function getCoursesCountAttribute()
    {
        return $this->courses()->count();
    }

    public function getActiveCoursesCountAttribute()
    {
        return $this->activeCourses()->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Route model binding
    public function getRouteKeyName()
    {
        return 'slug';
    }
}