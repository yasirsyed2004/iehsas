<?php
// File: app/Http/Requests/StudentRegistrationRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Student;

class StudentRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all users to register for entry test
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // NEW: Course Selection Validation
            'courses' => 'required|array|min:1',
            'courses.*' => 'exists:courses,id',
            // Basic Information
            'full_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'father_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:255',
            'contact_number' => [
                'required',
                'string',
                'max:20',
                'regex:/^03[0-9]{9}$/'
            ],
            'date_of_birth' => 'required|date|before:today',
            
            // ID Information (Dynamic validation)
            'id_type' => 'required|in:cnic,passport,driving_license',
            'id_number' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $this->validateIdNumber($attribute, $value, $fail);
                }
            ],
            
            // Location & Demographics
            'nationality' => 'required|string|max:100',
            'gender' => 'required|in:male,female,other',
            'qualification' => 'required|string|max:255',
            'home_address' => 'required|string|max:1000'
        ];
    }

    /**
     * Custom validation for ID number based on ID type
     */
    protected function validateIdNumber($attribute, $value, $fail)
    {
        $idType = $this->input('id_type');
        
        if (!$idType) {
            $fail('Please select an ID type first.');
            return;
        }

        // Clean the input
        $cleanedValue = $this->cleanIdNumber($value, $idType);
        
        // Validate format based on ID type
        if (!$this->isValidFormat($idType, $cleanedValue)) {
            $fail($this->getFormatErrorMessage($idType));
            return;
        }

        // Check for duplicate ID combination
        if ($this->isDuplicateId($idType, $cleanedValue)) {
            $fail($this->getDuplicateErrorMessage($idType));
            return;
        }
    }

    /**
     * Clean ID number based on type
     */
    protected function cleanIdNumber($value, $idType)
    {
        if ($idType === 'cnic') {
            // For CNIC, remove all non-digits first, then format
            $digits = preg_replace('/\D/', '', $value);
            if (strlen($digits) === 13) {
                return substr($digits, 0, 5) . '-' . substr($digits, 5, 7) . '-' . substr($digits, 12, 1);
            }
            return $value; // Return as-is if not 13 digits
        }
        
        // For passport and driving license, remove spaces and convert to uppercase
        return strtoupper(preg_replace('/\s+/', '', $value));
    }

    /**
     * Validate ID format based on type
     */
    protected function isValidFormat($idType, $value)
    {
        $patterns = [
            'cnic' => '/^[0-9]{5}-[0-9]{7}-[0-9]$/',
            'passport' => '/^[A-Z]{2}[0-9]{7}$/',
            'driving_license' => '/^[A-Z0-9]{8,15}$/'
        ];

        return isset($patterns[$idType]) && preg_match($patterns[$idType], $value);
    }

    /**
     * Check if ID combination already exists
     */
    protected function isDuplicateId($idType, $idNumber)
    {
        $existingStudent = Student::where('id_type', $idType)
                                 ->where('id_number', $idNumber)
                                 ->first();

        if (!$existingStudent) {
            return false; // No duplicate found
        }

        // If student exists, check if they can attempt test again
        return !$existingStudent->canAttemptTest();
    }

    /**
     * Get format error message based on ID type
     */
    protected function getFormatErrorMessage($idType)
    {
        return match($idType) {
            'cnic' => 'CNIC must be in format: 12345-1234567-1 (13 digits with dashes)',
            'passport' => 'Passport must be in format: AB1234567 (2 uppercase letters + 7 digits)',
            'driving_license' => 'Driving License must be 8-15 alphanumeric characters',
            default => 'Invalid ID format'
        };
    }

    /**
     * Get duplicate error message based on ID type
     */
    protected function getDuplicateErrorMessage($idType)
    {
        $idLabel = match($idType) {
            'cnic' => 'CNIC',
            'passport' => 'Passport',
            'driving_license' => 'Driving License',
            default => 'ID'
        };

        return "A student with this {$idLabel} has already attempted the test. Contact admin for retake permission.";
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            // NEW: Course validation messages
            'courses.required' => 'Please select at least one course.',
            'courses.array' => 'Invalid course selection format.',
            'courses.min' => 'Please select at least one course.',
            'courses.*.exists' => 'One or more selected courses are invalid.',

            // Name validations
            'full_name.required' => 'Full name is required.',
            'full_name.regex' => 'Full name can only contain letters and spaces.',
            'father_name.required' => 'Father\'s name is required.',
            'father_name.regex' => 'Father\'s name can only contain letters and spaces.',
            
            // Contact validations
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'contact_number.required' => 'Contact number is required.',
            'contact_number.regex' => 'Contact number must be in format: 03XXXXXXXXX (11 digits starting with 03)',
            
            // Date validation
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Please enter a valid date.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            
            // ID validations
            'id_type.required' => 'Please select an ID type.',
            'id_type.in' => 'Please select a valid ID type.',
            'id_number.required' => 'ID number is required.',
            
            // Demographics
            'nationality.required' => 'Nationality is required.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Please select a valid gender.',
            'qualification.required' => 'Qualification is required.',
            'home_address.required' => 'Home address is required.',
            'home_address.max' => 'Home address cannot exceed 1000 characters.'
        ];
    }

    /**
     * Get custom attributes for error messages
     */
    public function attributes(): array
    {
        return [
            'courses' => 'course selection',
            'full_name' => 'full name',
            'father_name' => 'father\'s name',
            'contact_number' => 'contact number',
            'date_of_birth' => 'date of birth',
            'id_type' => 'ID type',
            'id_number' => 'ID number',
            'home_address' => 'home address'
        ];
    }

    /**
     * Handle a failed validation attempt (customize response if needed)
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // You can customize the failed validation response here if needed
        parent::failedValidation($validator);
    }

    /**
     * Get the cleaned and formatted data after validation
     */
    public function getCleanedData(): array
    {
        $validated = $this->validated();
        
        // Format the ID number based on type
        $validated['id_number'] = $this->cleanIdNumber(
            $validated['id_number'], 
            $validated['id_type']
        );
        
        // Trim string fields
        $stringFields = ['full_name', 'father_name', 'email', 'nationality', 'qualification', 'home_address'];
        foreach ($stringFields as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = trim($validated[$field]);
            }
        }
        
        return $validated;
    }
}