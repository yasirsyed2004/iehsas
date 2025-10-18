<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnrollmentVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_type' => 'required|in:cnic,passport,driving_license',
            'id_number' => [
                'required',
                'string',
                $this->getIdNumberRule(),
            ],
            'verification_code' => [
                'required',
                'string',
                'size:4',
                'regex:/^[0-9]{4}$/',
            ],
        ];
    }

    /**
     * Get ID number validation rule based on ID type
     */
    private function getIdNumberRule(): string
    {
        $idType = $this->input('id_type');
        
        return match($idType) {
            'cnic' => 'regex:/^[0-9]{5}-?[0-9]{7}-?[0-9]{1}$/',
            'passport' => 'regex:/^[A-Z]{2}[0-9]{7}$/',
            'driving_license' => 'regex:/^[A-Z0-9]{8,15}$/',
            default => 'string'
        };
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        $idType = $this->input('id_type');
        
        $idMessages = match($idType) {
            'cnic' => [
                'id_number.regex' => 'CNIC must be in format: 12345-1234567-1 (13 digits with or without dashes)',
            ],
            'passport' => [
                'id_number.regex' => 'Passport must be in format: AB1234567 (2 letters followed by 7 digits)',
            ],
            'driving_license' => [
                'id_number.regex' => 'Driving License must be 8-15 alphanumeric characters',
            ],
            default => [
                'id_number.regex' => 'Invalid ID number format',
            ]
        };

        return array_merge([
            'id_type.required' => 'Please select an ID type.',
            'id_type.in' => 'Invalid ID type selected.',
            'id_number.required' => 'ID number is required.',
            'verification_code.required' => 'Verification code is required.',
            'verification_code.size' => 'Verification code must be exactly 4 digits.',
            'verification_code.regex' => 'Verification code must contain only numbers.',
        ], $idMessages);
    }

    /**
     * Get custom attributes for validation errors
     */
    public function attributes(): array
    {
        return [
            'id_type' => 'ID type',
            'id_number' => 'ID number',
            'verification_code' => 'verification code',
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        $data = [];
        
        // Clean ID number based on type
        if ($this->has('id_number') && $this->has('id_type')) {
            $idNumber = $this->input('id_number');
            $idType = $this->input('id_type');
            
            $data['id_number'] = $this->cleanIdNumber($idNumber, $idType);
        }
        
        // Clean verification code
        if ($this->has('verification_code')) {
            $data['verification_code'] = preg_replace('/[^0-9]/', '', $this->input('verification_code'));
        }
        
        if (!empty($data)) {
            $this->merge($data);
        }
    }

    /**
     * Clean ID number based on type
     */
    private function cleanIdNumber(string $idNumber, string $idType): string
    {
        return match($idType) {
            'cnic' => preg_replace('/[^0-9]/', '', $idNumber),
            'passport', 'driving_license' => strtoupper(trim($idNumber)),
            default => trim($idNumber)
        };
    }
}