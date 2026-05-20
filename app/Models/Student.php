<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Student extends Model
{
    use HasFactory;

    // Qualification constants
    const QUALIFICATION_INTERMEDIATE = 'intermediate';
    const QUALIFICATION_ADP = 'adp';
    const QUALIFICATION_BS_16 = 'bs_16';
    const QUALIFICATION_MS = 'ms';
    const QUALIFICATION_PHD = 'phd';
    const QUALIFICATION_OTHER = 'other';

    const QUALIFICATIONS = [
        self::QUALIFICATION_INTERMEDIATE => 'ICS/FSc/DAE (Intermediate)',
        self::QUALIFICATION_ADP => 'BA/B.Com/BSc/ADP (14 Years)',
        self::QUALIFICATION_BS_16 => 'BS/BE/BBA (16 Years)',
        self::QUALIFICATION_MS => 'MS/MBA/MSc/MPhil',
        self::QUALIFICATION_PHD => 'PhD',
        self::QUALIFICATION_OTHER => 'Other (specify)',
    ];

    // Eligibility proof keys (sub_type values stored in enrollment_documents)
    const PROOF_IOSH_OSHA = 'iosh_osha_certificate';
    const PROOF_ENGLISH_LANGUAGE = 'english_language_course';
    const PROOF_NEBOSH_HSA = 'nebosh_hsa_certificate';
    const PROOF_SAFETY_OFFICER_L3 = 'safety_officer_level3_ohs';
    const PROOF_HSE_EXPERIENCE = 'hse_experience_proof';

    // Registry of all proof options with their document_type and display label
    const PROOF_OPTIONS = [
        self::PROOF_IOSH_OSHA => [
            'document_type' => 'education',
            'label' => 'IOSH + OSHA Certificate',
        ],
        self::PROOF_ENGLISH_LANGUAGE => [
            'document_type' => 'education',
            'label' => 'English Language Course Certificate',
        ],
        self::PROOF_NEBOSH_HSA => [
            'document_type' => 'education',
            'label' => 'NEBOSH HSA Certificate',
        ],
        self::PROOF_SAFETY_OFFICER_L3 => [
            'document_type' => 'education',
            'label' => 'Safety Officer Level 3 in OHS',
        ],
        self::PROOF_HSE_EXPERIENCE => [
            'document_type' => 'cv',
            'label' => '2 Years HSE Experience Proof',
        ],
    ];

    // Session mode constants
    const SESSION_ONLINE = 'online';
    const SESSION_PHYSICAL = 'physical';

    const SESSION_MODES = [
        self::SESSION_ONLINE => 'Online Session',
        self::SESSION_PHYSICAL => 'Physical (Classroom) Session',
    ];

    protected $fillable = [
        'full_name',
        'father_name',
        'email',
        'contact_number',
        'date_of_birth',
        'id_type',
        'id_number',
        'nationality',
        'gender',
        'qualification',
        'session_mode',
        'qualification_other',
        'home_address',
        'is_retake_allowed',
        'enrollment_verification_code',
        'enrollment_code_generated_at',
        'enrollment_code_used',
        'enrollment_form_submitted',
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

    public function selectedCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'student_course_selections')
            ->withTimestamps()
            ->withPivot('selected_at');
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
     * Get qualification display label (handles both dropdown values and legacy free-text)
     */
    public function getQualificationLabelAttribute(): string
    {
        if (isset(self::QUALIFICATIONS[$this->qualification])) {
            $label = self::QUALIFICATIONS[$this->qualification];
            if ($this->qualification === self::QUALIFICATION_OTHER && $this->qualification_other) {
                return $label . ': ' . $this->qualification_other;
            }
            return $label;
        }
        return $this->qualification ?? '';
    }

    /**
     * Get session mode display label
     */
    public function getSessionModeLabelAttribute(): string
    {
        return self::SESSION_MODES[$this->session_mode] ?? ucfirst($this->session_mode ?? 'Not specified');
    }

    /**
     * Get the proof option keys that are acceptable for this student's
     * current session_mode + qualification combination.
     *
     * Physical + Intermediate/Other  -> ANY ONE of 5 options (4 certs OR 2yr exp)
     * Online   + Intermediate/ADP/Other -> 2-year HSE experience proof
     * Everything else (ADP+Physical, BS_16/MS/PhD any mode) -> none required
     */
    public function getAcceptedProofKeys(): array
    {
        if (!$this->session_mode) {
            return [];
        }

        if ($this->session_mode === self::SESSION_PHYSICAL) {
            if (in_array($this->qualification, [self::QUALIFICATION_INTERMEDIATE, self::QUALIFICATION_OTHER])) {
                return [
                    self::PROOF_IOSH_OSHA,
                    self::PROOF_ENGLISH_LANGUAGE,
                    self::PROOF_NEBOSH_HSA,
                    self::PROOF_SAFETY_OFFICER_L3,
                    self::PROOF_HSE_EXPERIENCE,
                ];
            }
        }

        if ($this->session_mode === self::SESSION_ONLINE) {
            if (in_array($this->qualification, [self::QUALIFICATION_INTERMEDIATE, self::QUALIFICATION_ADP, self::QUALIFICATION_OTHER])) {
                return [self::PROOF_HSE_EXPERIENCE];
            }
        }

        return [];
    }

    /**
     * Check if eligibility proof upload is required
     */
    public function requiresEligibilityProof(): bool
    {
        return !empty($this->getAcceptedProofKeys());
    }

    /**
     * Get the required proof document type and sub_type.
     *
     * Kept for backward compatibility. Returns the single applicable proof
     * when only one option exists; returns null when multiple are accepted
     * (caller must explicitly choose a proof key).
     */
    public function getRequiredProofType(): ?array
    {
        $keys = $this->getAcceptedProofKeys();

        if (count($keys) !== 1) {
            return null;
        }

        $key = $keys[0];
        return [
            'document_type' => self::PROOF_OPTIONS[$key]['document_type'],
            'sub_type' => $key,
        ];
    }

    /**
     * Check if the required eligibility proof has been uploaded.
     * Returns true if at least ONE of the accepted proof types exists.
     */
    public function hasRequiredEligibilityProof(): bool
    {
        $keys = $this->getAcceptedProofKeys();

        if (empty($keys)) {
            return true;
        }

        foreach ($keys as $key) {
            $option = self::PROOF_OPTIONS[$key];
            $exists = $this->enrollmentDocuments()
                ->where('document_type', $option['document_type'])
                ->where('sub_type', $key)
                ->exists();

            if ($exists) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get human-readable eligibility message
     */
    public function getEligibilityMessage(): ?string
    {
        if ($this->session_mode === self::SESSION_PHYSICAL) {
            if (in_array($this->qualification, [self::QUALIFICATION_INTERMEDIATE, self::QUALIFICATION_OTHER])) {
                return 'For Physical session with your qualification, please upload ONE of the following: IOSH+OSHA Certificate, English Language Course Certificate, NEBOSH HSA Certificate, Safety Officer Level 3 in OHS, OR proof of 2 years relevant HSE experience.';
            }
        }

        if ($this->session_mode === self::SESSION_ONLINE) {
            if (in_array($this->qualification, [self::QUALIFICATION_INTERMEDIATE, self::QUALIFICATION_ADP, self::QUALIFICATION_OTHER])) {
                return 'For Online session with your qualification, please upload proof of minimum 2 years relevant HSE experience.';
            }
        }

        return null;
    }

    /**
     * Check if student is eligible for enrollment
     */
    public function isEligibleForEnrollment(): bool
    {
        return $this->hasPassedEntryTest()
            && !$this->enrollment_form_submitted
            && $this->hasRequiredEligibilityProof();
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
    // Define required documents with their sub-types
    $requiredDocuments = [
        // Identity: Both CNIC front and back required
        ['type' => 'identity', 'sub_type' => 'cnic_front'],
        ['type' => 'identity', 'sub_type' => 'cnic_back'],
        
        // Education: Only Matric is required
        ['type' => 'education', 'sub_type' => 'matric'],
        
        // CV: Main CV is required
        ['type' => 'cv', 'sub_type' => 'cv'],
        
        // Photo: No sub-type
        ['type' => 'photo', 'sub_type' => null],
    ];

    foreach ($requiredDocuments as $required) {
        $query = $this->enrollmentDocuments()
            ->where('document_type', $required['type']);
        
        if ($required['sub_type'] !== null) {
            $query->where('sub_type', $required['sub_type']);
        }
        
        if (!$query->exists()) {
            return false;
        }
    }

    return true;
}

/**
 * ADD: Get document by type and sub-type
 */
public function getDocumentByTypeAndSubType(string $type, ?string $subType = null): ?EnrollmentDocument
{
    $query = $this->enrollmentDocuments()->where('document_type', $type);
    
    if ($subType !== null) {
        $query->where('sub_type', $subType);
    } else {
        $query->whereNull('sub_type');
    }
    
    return $query->first();
}

/**
 * ADD: Get all documents grouped by type and sub-type
 */
public function getGroupedDocuments(): array
{
    $documents = $this->enrollmentDocuments;
    $grouped = [];
    
    foreach ($documents as $document) {
        $type = $document->document_type;
        $subType = $document->sub_type ?? 'main';
        
        if (!isset($grouped[$type])) {
            $grouped[$type] = [];
        }
        
        $grouped[$type][$subType] = $document;
    }
    
    return $grouped;
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