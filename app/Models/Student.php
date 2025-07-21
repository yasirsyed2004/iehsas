<?php
// File: app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'contact_number',
        'cnic',
        'gender',
        'qualification',
        'is_retake_allowed'
    ];

    protected $casts = [
        'is_retake_allowed' => 'boolean'
    ];

    // Relationships
    public function entryTestAttempts()
    {
        return $this->hasMany(EntryTestAttempt::class);
    }

    // Check if student can attempt test
    public function canAttemptTest()
    {
        $hasAttempted = $this->entryTestAttempts()->where('status', 'completed')->exists();
        
        if (!$hasAttempted) {
            return true;
        }
        
        return $this->is_retake_allowed;
    }

    // Get latest attempt
    public function latestAttempt()
    {
        return $this->entryTestAttempts()->latest()->first();
    }

    // Check if passed any test
    public function hasPassed()
    {
        return $this->entryTestAttempts()
            ->where('status', 'completed')
            ->whereNotNull('percentage')
            ->whereRaw('percentage >= (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)')
            ->exists();
    }

    // Scopes
    public function scopeByCnic($query, $cnic)
    {
        return $query->where('cnic', $cnic);
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }
}