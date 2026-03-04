<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyActivityReport extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'reporting_manager_id',
        'report_date',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'reviewer_comment',
        'is_locked',
    ];

    protected $casts = [
        'report_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'is_locked' => 'boolean',
    ];

    // --- Relationships ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function reportingManager(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reporting_manager_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(DarEntry::class, 'daily_activity_report_id')->orderBy('sort_order');
    }

    public function addendums(): HasMany
    {
        return $this->hasMany(DarAddendum::class, 'daily_activity_report_id')->orderBy('created_at');
    }

    // --- Scopes ---

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('report_date', $date);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('report_date', $year)->whereMonth('report_date', $month);
    }

    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // --- Helper Methods ---

    public function submit(): void
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'is_locked' => true,
        ]);
    }

    public function isEditable(): bool
    {
        return !$this->is_locked && in_array($this->status, ['draft', 'revision_requested']);
    }

    public function canAddAddendum(): bool
    {
        return $this->status === 'submitted' || $this->status === 'reviewed';
    }

    public function canBeReviewed(): bool
    {
        return $this->status === 'submitted';
    }

    // --- Accessors ---

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'reviewed' => 'Reviewed',
            'revision_requested' => 'Revision Requested',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'submitted' => 'primary',
            'reviewed' => 'success',
            'revision_requested' => 'warning',
            default => 'secondary',
        };
    }

    public static function getStatuses(): array
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'reviewed' => 'Reviewed',
            'revision_requested' => 'Revision Requested',
        ];
    }
}
