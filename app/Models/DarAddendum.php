<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DarAddendum extends Model
{
    protected $fillable = [
        'daily_activity_report_id',
        'content',
        'added_by',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyActivityReport::class, 'daily_activity_report_id');
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
