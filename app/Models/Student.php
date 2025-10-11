<?php
// File: app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'enrollment_verification_code',     // Future Phase 2
        'enrollment_code_generated_at',     // Future Phase 2
        'enrollment_code_used',            // Future Phase 2
        'enrollment_form_submitted'        // Future Phase 2
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_code_generated_at' => 'datetime',
        'enrollment_code_used' => 'boolean',
        'enrollment_form_submitted' => 'boolean',
        'is_retake_allowed' => 'boolean'
    ];

    // Relationships
    public function entryTestAttempts()
    {
        return $this->hasMany(EntryTestAttempt::class);
    }

    // NEW HELPER METHODS for ID Management

    /**
     * Get formatted ID display (e.g., "12345-1234567-1" for CNIC)
     */
    public function getFormattedIdAttribute()
    {
        if ($this->id_type === 'cnic' && strlen(str_replace('-', '', $this->id_number)) === 13) {
            $cleaned = str_replace('-', '', $this->id_number);
            return substr($cleaned, 0, 5) . '-' . substr($cleaned, 5, 7) . '-' . substr($cleaned, 12, 1);
        }
        
        return strtoupper($this->id_number);
    }

    /**
     * Get ID type label for display
     */
    public function getIdTypeLabelAttribute()
    {
        return match($this->id_type) {
            'cnic' => 'CNIC',
            'passport' => 'Passport',
            'driving_license' => 'Driving License',
            default => 'Unknown'
        };
    }

    /**
     * Scope to find by ID type and number combination
     */
    public function scopeByIdTypeAndNumber($query, $type, $number)
    {
        return $query->where('id_type', $type)->where('id_number', $number);
    }

    /**
     * Generate 4-digit enrollment verification code (for Phase 2)
     */
    public function generateEnrollmentCode()
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
     * Verify enrollment code (for Phase 2)
     */
    public function verifyEnrollmentCode($inputCode)
    {
        if ($this->enrollment_code_used) {
            return false; // Code already used
        }
        
        if ($this->enrollment_verification_code !== $inputCode) {
            return false; // Invalid code
        }
        
        // Mark code as used
        $this->update(['enrollment_code_used' => true]);
        return true;
    }

    /**
     * Check if enrollment code is valid and unused
     */
    public function hasValidEnrollmentCode()
    {
        return !empty($this->enrollment_verification_code) && 
               !$this->enrollment_code_used &&
               !empty($this->enrollment_code_generated_at);
    }

    // EXISTING METHODS (Updated for new structure)

    /**
     * Check if student can attempt test
     */
    public function canAttemptTest()
    {
        $hasAttempted = $this->entryTestAttempts()->where('status', 'completed')->exists();
        
        if (!$hasAttempted) {
            return true;
        }
        
        return $this->is_retake_allowed;
    }

    /**
     * Get latest attempt
     */
    public function latestAttempt()
    {
        return $this->entryTestAttempts()->latest()->first();
    }

    /**
     * Check if passed any test
     */
    public function hasPassed()
    {
        return $this->entryTestAttempts()
            ->where('status', 'completed')
            ->whereNotNull('percentage')
            ->whereRaw('percentage >= (SELECT passing_score FROM entry_tests WHERE id = entry_test_attempts.entry_test_id)')
            ->exists();
    }

    // UPDATED SCOPES (replacing CNIC-based scopes)

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

    // VALIDATION HELPERS

    /**
     * Static method to validate ID format based on type
     */
    public static function validateIdFormat($idType, $idNumber)
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
    public static function formatIdNumber($idType, $rawNumber)
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