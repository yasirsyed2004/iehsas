<?php

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

    public function entryTest()
    {
        return $this->belongsTo(EntryTest::class);
    }
}