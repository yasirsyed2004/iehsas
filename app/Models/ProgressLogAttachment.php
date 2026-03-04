<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressLogAttachment extends Model
{
    protected $fillable = [
        'progress_log_id',
        'original_filename',
        'stored_filename',
        'file_path',
        'file_size',
        'mime_type',
    ];

    public function progressLog(): BelongsTo
    {
        return $this->belongsTo(TaskProgressLog::class, 'progress_log_id');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    public function getFileIconAttribute(): string
    {
        $extension = pathinfo($this->original_filename, PATHINFO_EXTENSION);

        return match(strtolower($extension)) {
            'pdf' => 'fa-file-pdf text-danger',
            'doc', 'docx' => 'fa-file-word text-primary',
            'xls', 'xlsx' => 'fa-file-excel text-success',
            'ppt', 'pptx' => 'fa-file-powerpoint text-warning',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'fa-file-image text-info',
            'zip', 'rar', '7z' => 'fa-file-archive text-secondary',
            'txt' => 'fa-file-alt text-muted',
            default => 'fa-file text-secondary',
        };
    }
}
