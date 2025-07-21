<?php
// File: app/Models/EntryTestAttempt.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryTestAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'entry_test_id',
        'started_at',
        'completed_at',
        'expires_at',
        'total_marks',
        'obtained_marks',
        'percentage',
        'status',
        'proctoring_violations',
        'browser_switches'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'proctoring_violations' => 'array'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function entryTest()
    {
        return $this->belongsTo(EntryTest::class);
    }

    public function answers()
    {
        return $this->hasMany(EntryTestAnswer::class);
    }

    // Helper methods
    public function isExpired()
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }

    public function timeRemaining()
    {
        if (!$this->expires_at) {
            return null;
        }
        
        return $this->expires_at->diffInMinutes(now(), false);
    }

    public function hasPassed()
    {
        return $this->percentage >= $this->entryTest->passing_score;
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopePassed($query)
    {
        return $query->whereRaw('percentage >= (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)');
    }
}