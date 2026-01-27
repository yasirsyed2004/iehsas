<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'name',
        'description',
        'head_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function head(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'head_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getTaskCountAttribute()
    {
        return $this->tasks()->count();
    }

    public function getPendingTasksCountAttribute()
    {
        return $this->tasks()->where('status', 'pending')->count();
    }

    public function getInProgressTasksCountAttribute()
    {
        return $this->tasks()->where('status', 'in_progress')->count();
    }

    public function getCompletedTasksCountAttribute()
    {
        return $this->tasks()->where('status', 'completed')->count();
    }
}
