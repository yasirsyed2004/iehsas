<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EnrollmentDocument extends Model
{
    protected $fillable = [
        'student_id',
        'document_type',
        'original_filename',
        'stored_filename',
        'file_path',
        'file_size',
        'mime_type',
        'status',
        'admin_comments',
        'uploaded_at',
        'reviewed_at',
        'reviewed_by'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'file_size' => 'integer'
    ];

    // Document types
    const TYPE_IDENTITY = 'identity';
    const TYPE_EDUCATION = 'education';
    const TYPE_CV = 'cv';
    const TYPE_PHOTO = 'photo';

    // Document statuses
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the student that owns the document
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the admin who reviewed the document
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get document type label
     */
    public function getDocumentTypeLabelAttribute(): string
    {
        return match($this->document_type) {
            self::TYPE_IDENTITY => 'Identity Document (CNIC/Passport/Form-B)',
            self::TYPE_EDUCATION => 'Educational Certificates',
            self::TYPE_CV => 'CV/Resume',
            self::TYPE_PHOTO => 'Recent Photograph',
            default => 'Unknown Document Type'
        };
    }

    /**
     * Get status label with styling classes
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Under Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Needs Revision',
            default => 'Unknown Status'
        };
    }

    /**
     * Get status color class for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'text-yellow-600 bg-yellow-100',
            self::STATUS_APPROVED => 'text-green-600 bg-green-100',
            self::STATUS_REJECTED => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Check if document is an image
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Get file URL for viewing
     */
    public function getFileUrlAttribute(): string
    {
        return route('enrollment.document.view', $this->id);
    }

    /**
     * Get download URL
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('enrollment.document.download', $this->id);
    }

    /**
     * Check if file exists in storage
     */
    public function fileExists(): bool
    {
        return Storage::disk('enrollment')->exists($this->file_path);
    }

    /**
     * Get file contents
     */
    public function getFileContents()
    {
        if (!$this->fileExists()) {
            return null;
        }
        
        return Storage::disk('enrollment')->get($this->file_path);
    }

    /**
     * Delete file from storage
     */
    public function deleteFile(): bool
    {
        if ($this->fileExists()) {
            return Storage::disk('enrollment')->delete($this->file_path);
        }
        
        return true;
    }

    /**
     * Get all document types
     */
    public static function getDocumentTypes(): array
    {
        return [
            self::TYPE_IDENTITY => 'Identity Document (CNIC/Passport/Form-B)',
            self::TYPE_EDUCATION => 'Educational Certificates',
            self::TYPE_CV => 'CV/Resume',
            self::TYPE_PHOTO => 'Recent Photograph',
        ];
    }

    /**
     * Get all statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Under Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Needs Revision',
        ];
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by document type
     */
    public function scopeDocumentType($query, $type)
    {
        return $query->where('document_type', $type);
    }
}