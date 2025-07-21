<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryTest extends Model
{
    protected $fillable = [
        'title', 'description', 'duration_minutes', 'total_questions', 
        'passing_score', 'is_active'
    ];

    public function questions()
    {
        return $this->hasMany(EntryTestQuestion::class);
    }

    public function attempts()
    {
        return $this->hasMany(EntryTestAttempt::class);
    }
}