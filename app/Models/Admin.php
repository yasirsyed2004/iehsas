<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

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

    // Check admin role
    public function hasRole($role)
    {
        return $this->role === $role;
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
}