<?php
// File: app/Models/EntryTestAttempt.php
// REPLACE THE ENTIRE MODEL WITH THIS UPDATED VERSION:

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryTestAttempt extends Model
{
    protected $fillable = [
        'user_id',           // Nullable - for authenticated users
        'student_id',        // For guest students (entry test registration)
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
        return $this->belongsTo(User::class)->nullable();
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

    // Helper Methods
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

    // Get the participant (either user or student)
    public function getParticipantAttribute()
    {
        return $this->user ?? $this->student;
    }

    public function getParticipantNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }
        
        if ($this->student) {
            return $this->student->full_name;
        }
        
        return 'Unknown';
    }

    public function getParticipantEmailAttribute()
    {
        if ($this->user) {
            return $this->user->email;
        }
        
        if ($this->student) {
            return $this->student->email;
        }
        
        return null;
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

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}