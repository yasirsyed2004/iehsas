<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean'
    ];

    // Check if admin is active
    public function isActive()
    {
        return $this->status;
    }

    // Check admin string role (renamed to avoid conflict with Spatie's hasRole)
    public function hasStringRole($role)
    {
        return $this->role === $role;
    }

    // Check if admin is super admin
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    // Get admin roles
    public static function getRoles()
    {
        return [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'moderator' => 'Moderator'
        ];
    }

    // Task Management Relationships
    public function headOfDepartments()
    {
        return $this->hasMany(Department::class, 'head_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    public function taskComments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'notifiable_id')
            ->where('notifiable_type', 'admin');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }
}