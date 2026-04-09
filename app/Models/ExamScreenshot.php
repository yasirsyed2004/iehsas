<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ExamScreenshot extends Model
{
    protected $fillable = [
        'entry_test_attempt_id',
        'file_path',
        'file_size',
        'captured_at',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'file_size' => 'integer',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(EntryTestAttempt::class, 'entry_test_attempt_id');
    }

    public function getImageUrl(): ?string
    {
        if (Storage::disk('enrollment')->exists($this->file_path)) {
            return route('exam-screenshot.show', $this->id);
        }
        return null;
    }
}
