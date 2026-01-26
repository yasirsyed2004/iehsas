<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskComment extends Model
{
    protected $fillable = [
        'task_id',
        'admin_id',
        'user_id',
        'comment'
    ];

    // Relationships
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessor for commenter name
    public function getCommenterNameAttribute()
    {
        if ($this->admin_id) {
            return $this->admin?->name ?? 'Admin';
        }
        return $this->user?->name ?? 'User';
    }

    public function getCommenterTypeAttribute()
    {
        return $this->admin_id ? 'admin' : 'user';
    }
}
