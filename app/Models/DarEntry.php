<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DarEntry extends Model
{
    protected $fillable = [
        'daily_activity_report_id',
        'time_from',
        'time_to',
        'activity_description',
        'related_task_id',
        'remarks',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyActivityReport::class, 'daily_activity_report_id');
    }

    public function relatedTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'related_task_id');
    }

    public function getTimeRangeAttribute(): string
    {
        return date('h:i A', strtotime($this->time_from)) . ' - ' . date('h:i A', strtotime($this->time_to));
    }
}
