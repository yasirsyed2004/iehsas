<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'father_name',          // NEW
        'email',
        'contact_number',
        'date_of_birth',        // NEW
        'id_type',              // REPLACES cnic
        'id_number',            // REPLACES cnic value
        'nationality',          // NEW
        'gender',
        'qualification',
        'home_address',         // NEW
        'is_retake_allowed',
        'enrollment_verification_code',     // Phase 2
        'enrollment_code_generated_at',     // Phase 2
        'enrollment_code_used',            // Phase 2
        'enrollment_form_submitted'        // Phase 2
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_code_generated_at' => 'datetime',
        'enrollment_code_used' => 'boolean',
        'enrollment_form_submitted' => 'boolean',
        'is_retake_allowed' => 'boolean'
    ];

    // RELATIONSHIPS

    /**
     * Get enrollment documents for this student (Phase 2)
     */
    public function enrollmentDocuments(): HasMany
    {
        return $this->hasMany(EnrollmentDocument::class);
    }

    /**
     * Get entry test attempts
     */
    public function entryTestAttempts(): HasMany
    {
        return $this->hasMany(EntryTestAttempt::class);
    }

    /**
     * Get all test attempts (alias for compatibility)
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(EntryTestAttempt::class);
    }

    /**
     * Get the latest test attempt
     */
    public function latestAttempt(): HasOne
    {
        return $this->hasOne(EntryTestAttempt::class)->latestOfMany();
    }

    // PHASE 2 ENROLLMENT METHODS

    /**
     * Generate enrollment verification code
     */
    public function generateEnrollmentCode(): string
    {
        $code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        
        $this->update([
            'enrollment_verification_code' => $code,
            'enrollment_code_generated_at' => now(),
            'enrollment_code_used' => false
        ]);
        
        return $code;
    }

    /**
     * Verify enrollment code
     */
    public function verifyEnrollmentCode(string $code): bool
    {
        // Check if code matches
        if ($this->enrollment_verification_code !== $code) {
            return false;
        }

        // Check if code is not already used
        if ($this->enrollment_code_used) {
            return false;
        }

        // Check if code is not expired
        if ($this->isEnrollmentCodeExpired()) {
            return false;
        }

        return true;
    }

    /**
     * Mark enrollment code as used
     */
    public function markEnrollmentCodeAsUsed(): void
    {
        $this->update(['enrollment_code_used' => true]);
    }

    /**
     * Check if enrollment code is expired
     */
    public function isEnrollmentCodeExpired(): bool
    {
        if (!$this->enrollment_code_generated_at) {
            return true;
        }

        // FIXED: Cast configuration value to integer to prevent Carbon TypeError
$expiryHours = (int) config('mail.enrollment.verification_code_expiry_hours', 48);

// Use copy() to avoid mutating the original Carbon instance
$expiryTime = $this->enrollment_code_generated_at->copy()->addHours($expiryHours);
        
        return now()->isAfter($expiryTime);
    }

    /**
     * Check if student has passed the entry test
     */
    public function hasPassedEntryTest(): bool
    {
        return $this->entryTestAttempts()
            ->where('status', 'completed')
            ->whereNotNull('percentage')
            ->whereRaw('percentage >= (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)')
            ->exists();
    }

    /**
     * Check if student is eligible for enrollment
     */
    public function isEligibleForEnrollment(): bool
    {
        return $this->hasPassedEntryTest() && !$this->enrollment_form_submitted;
    }

    /**
     * Check if enrollment code has been sent
     */
    public function hasEnrollmentCodeBeenSent(): bool
    {
        return !is_null($this->enrollment_verification_code) && 
               !is_null($this->enrollment_code_generated_at);
    }

    /**
     * Get enrollment status
     */
    public function getEnrollmentStatusAttribute(): string
    {
        if (!$this->hasPassedEntryTest()) {
            return 'not_eligible';
        }

        if ($this->enrollment_form_submitted) {
            return 'completed';
        }

        if ($this->hasEnrollmentCodeBeenSent()) {
            if ($this->enrollment_code_used) {
                return 'in_progress';
            } elseif ($this->isEnrollmentCodeExpired()) {
                return 'code_expired';
            } else {
                return 'code_sent';
            }
        }

        return 'eligible';
    }

    /**
     * Get enrollment status label
     */
    public function getEnrollmentStatusLabelAttribute(): string
    {
        return match($this->enrollment_status) {
            'not_eligible' => 'Not Eligible',
            'eligible' => 'Eligible for Enrollment',
            'code_sent' => 'Verification Code Sent',
            'code_expired' => 'Verification Code Expired',
            'in_progress' => 'Enrollment in Progress',
            'completed' => 'Enrollment Completed',
            default => 'Unknown Status'
        };
    }

    /**
     * Get enrollment documents by type
     */
    public function getDocumentByType(string $type): ?EnrollmentDocument
    {
        return $this->enrollmentDocuments()->where('document_type', $type)->first();
    }

    /**
     * Check if all required documents are uploaded
     */
    public function hasAllRequiredDocuments(): bool
    {
        $requiredTypes = ['identity', 'education', 'cv', 'photo'];
        $uploadedTypes = $this->enrollmentDocuments()->pluck('document_type')->toArray();
        
        return count(array_intersect($requiredTypes, $uploadedTypes)) === count($requiredTypes);
    }

    /**
     * Check if all documents are approved
     */
    public function areAllDocumentsApproved(): bool
    {
        if (!$this->hasAllRequiredDocuments()) {
            return false;
        }

        return $this->enrollmentDocuments()
                   ->where('status', '!=', 'approved')
                   ->count() === 0;
    }

    /**
     * Get document review status summary
     */
    public function getDocumentStatusSummary(): array
    {
        $documents = $this->enrollmentDocuments;
        
        return [
            'total' => $documents->count(),
            'pending' => $documents->where('status', 'pending')->count(),
            'approved' => $documents->where('status', 'approved')->count(),
            'rejected' => $documents->where('status', 'rejected')->count(),
        ];
    }


/**
 * Get document review status summary for admin display
 */
public function getDocumentsStatusAttribute(): ?array
{
    // If no documents uploaded, return null
    if ($this->enrollmentDocuments()->count() === 0) {
        return null;
    }

    $documents = $this->enrollmentDocuments;
    
    return [
        'total' => $documents->count(),
        'pending' => $documents->where('status', EnrollmentDocument::STATUS_PENDING)->count(),
        'approved' => $documents->where('status', EnrollmentDocument::STATUS_APPROVED)->count(),
        'rejected' => $documents->where('status', EnrollmentDocument::STATUS_REJECTED)->count(),
    ];
}

    /**
     * Mark enrollment form as submitted
     */
    public function markEnrollmentFormAsSubmitted(): void
    {
        $this->update(['enrollment_form_submitted' => true]);
    }

    // EXISTING HELPER METHODS (Enhanced)

    /**
     * Get formatted ID display (e.g., "12345-1234567-1" for CNIC)
     */
    public function getFormattedIdAttribute(): string
    {
        return match($this->id_type) {
            'cnic' => $this->formatCnic($this->id_number),
            'passport' => strtoupper($this->id_number),
            'driving_license' => strtoupper($this->id_number),
            default => $this->id_number
        };
    }

    /**
     * Format CNIC with dashes
     */
    private function formatCnic(string $cnic): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $cnic);
        
        if (strlen($cleaned) === 13) {
            return substr($cleaned, 0, 5) . '-' . substr($cleaned, 5, 7) . '-' . substr($cleaned, 12, 1);
        }
        
        return $cnic;
    }

    /**
     * Get ID type label for display
     */
    public function getIdTypeLabelAttribute(): string
    {
        return match($this->id_type) {
            'cnic' => 'CNIC',
            'passport' => 'Passport',
            'driving_license' => 'Driving License',
            default => 'ID'
        };
    }

    /**
     * Check if student can attempt test
     */
    public function canAttemptTest(): bool
    {
        $hasAttempted = $this->entryTestAttempts()->where('status', 'completed')->exists();
        
        if (!$hasAttempted) {
            return true;
        }
        
        return $this->is_retake_allowed;
    }

    /**
     * Get latest attempt (method version for compatibility)
     */
    public function latestAttemptMethod()
    {
        return $this->entryTestAttempts()->latest()->first();
    }

    /**
     * Check if passed any test (alias for hasPassedEntryTest)
     */
    public function hasPassed(): bool
    {
        return $this->hasPassedEntryTest();
    }

    /**
     * Check if enrollment code is valid and unused (updated for Phase 2)
     */
    public function hasValidEnrollmentCode(): bool
    {
        return !empty($this->enrollment_verification_code) && 
               !$this->enrollment_code_used &&
               !empty($this->enrollment_code_generated_at) &&
               !$this->isEnrollmentCodeExpired();
    }

    // SCOPES

    /**
     * Scope to find by ID type and number combination
     */
    public function scopeByIdTypeAndNumber($query, $type, $number)
    {
        return $query->where('id_type', $type)->where('id_number', $number);
    }

    /**
     * Find by ID (replaces byCnic scope)
     */
    public function scopeByIdentification($query, $idType, $idNumber)
    {
        return $query->where('id_type', $idType)->where('id_number', $idNumber);
    }

    /**
     * Find by email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Find by nationality
     */
    public function scopeByNationality($query, $nationality)
    {
        return $query->where('nationality', $nationality);
    }

    /**
     * Find students with specific ID type
     */
    public function scopeByIdType($query, $idType)
    {
        return $query->where('id_type', $idType);
    }

    /**
     * Scope for students eligible for enrollment
     */
    public function scopeEligibleForEnrollment($query)
    {
        return $query->whereHas('entryTestAttempts', function ($q) {
            $q->where('status', 'completed')
              ->whereNotNull('percentage')
              ->whereRaw('percentage >= (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)');
        })->where('enrollment_form_submitted', false);
    }

    /**
     * Scope for students with completed enrollment
     */
    public function scopeEnrollmentCompleted($query)
    {
        return $query->where('enrollment_form_submitted', true);
    }

    // VALIDATION HELPERS

    /**
     * Static method to validate ID format based on type
     */
    public static function validateIdFormat($idType, $idNumber): bool
    {
        $patterns = [
            'cnic' => '/^[0-9]{5}-[0-9]{7}-[0-9]$/',
            'passport' => '/^[A-Z]{2}[0-9]{7}$/',
            'driving_license' => '/^[A-Z0-9]{8,15}$/'
        ];

        if (!isset($patterns[$idType])) {
            return false;
        }

        return preg_match($patterns[$idType], $idNumber);
    }

    /**
     * Format ID number based on type
     */
    public static function formatIdNumber($idType, $rawNumber): string
    {
        $cleaned = preg_replace('/[^A-Z0-9]/i', '', $rawNumber);
        
        if ($idType === 'cnic' && strlen($cleaned) === 13) {
            return substr($cleaned, 0, 5) . '-' . 
                   substr($cleaned, 5, 7) . '-' . 
                   substr($cleaned, 12, 1);
        }
        
        return strtoupper($cleaned);
    }
}