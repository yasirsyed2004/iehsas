<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Get the notifiable entity (Admin or User)
    public function notifiable()
    {
        if ($this->notifiable_type === 'admin') {
            return $this->belongsTo(Admin::class, 'notifiable_id');
        }
        return $this->belongsTo(User::class, 'notifiable_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeForAdmin($query, $adminId)
    {
        return $query->where('notifiable_type', 'admin')
            ->where('notifiable_id', $adminId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('notifiable_type', 'user')
            ->where('notifiable_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Mark as read
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->is_read = true;
            $this->read_at = now();
            $this->save();
        }
    }

    // Get icon based on notification type
    public function getIconAttribute()
    {
        return match($this->type) {
            'task_assigned' => 'fa-tasks text-primary',
            'task_updated' => 'fa-edit text-info',
            'task_completed' => 'fa-check-circle text-success',
            'deadline_approaching' => 'fa-clock text-warning',
            'deadline_overdue' => 'fa-exclamation-circle text-danger',
            'comment_added' => 'fa-comment text-info',
            default => 'fa-bell text-secondary'
        };
    }

    // Static helper to create notification
    public static function createForAdmin($adminId, $type, $title, $message, $data = [])
    {
        return self::create([
            'notifiable_type' => 'admin',
            'notifiable_id' => $adminId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function createForUser($userId, $type, $title, $message, $data = [])
    {
        return self::create([
            'notifiable_type' => 'user',
            'notifiable_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }
}
