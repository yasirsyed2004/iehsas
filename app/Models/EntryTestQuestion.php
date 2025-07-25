<?php
// File: app/Models/EntryTestQuestion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryTestQuestion extends Model
{
    protected $fillable = [
        'entry_test_id', 'question_text', 'question_type', 
        'options', 'correct_answer', 'marks', 'order'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    // Relationships
    public function entryTest()
    {
        return $this->belongsTo(EntryTest::class);
    }

    public function answers()
    {
        return $this->hasMany(EntryTestAnswer::class, 'entry_test_question_id');
    }

    // Helper Methods
    public function getSuccessRateAttribute()
    {
        $totalAnswers = $this->answers()->count();
        if ($totalAnswers === 0) {
            return 0;
        }
        
        $correctAnswers = $this->answers()->where('is_correct', true)->count();
        return round(($correctAnswers / $totalAnswers) * 100, 1);
    }

    public function getTotalAttemptsAttribute()
    {
        return $this->answers()->count();
    }

    public function getCorrectAttemptsAttribute()
    {
        return $this->answers()->where('is_correct', true)->count();
    }

    public function getIncorrectAttemptsAttribute()
    {
        return $this->answers()->where('is_correct', false)->count();
    }

    // Scopes
    public function scopeByEntryTest($query, $entryTestId)
    {
        return $query->where('entry_test_id', $entryTestId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('question_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}