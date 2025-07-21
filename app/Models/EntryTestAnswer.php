<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryTestAnswer extends Model
{
    protected $fillable = [
        'entry_test_attempt_id', 'entry_test_question_id',
        'selected_answer', 'is_correct', 'marks_obtained'
    ];

    public function attempt()
    {
        return $this->belongsTo(EntryTestAttempt::class);
    }

    public function question()
    {
        return $this->belongsTo(EntryTestQuestion::class, 'entry_test_question_id');
    }
}