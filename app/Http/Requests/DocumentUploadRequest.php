<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get valid sub-types for a document type
     */
    private function getValidSubTypes(string $documentType): array
    {
        return match($documentType) {
            'identity' => ['cnic_front', 'cnic_back', 'passport', 'form_b'],
            'education' => ['matric', 'intermediate', 'bachelors', 'masters', 'other'],
            'cv' => ['cv', 'experience_letter', 'certificate'],
            'photo' => [],
            default => []
        };
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $documentType = $this->input('document_type');
        
        return [
            'document_type' => 'required|in:identity,education,cv,photo',
            'sub_type' => [
                'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($documentType) {
                    // Validate sub_type based on document_type
                    $validSubTypes = $this->getValidSubTypes($documentType);
                    
                    if ($documentType !== 'photo' && empty($value)) {
                        $fail('Sub-type is required for ' . $documentType . ' documents.');
                    }
                    
                    if (!empty($value) && !in_array($value, $validSubTypes)) {
                        $fail('Invalid sub-type for ' . $documentType . ' document.');
                    }
                },
            ],
            'document' => [
                'required',
                'file',
                $this->getFileSizeRule($documentType),
                $this->getMimeTypeRule($documentType),
            ],
        ];
    }

    /**
     * Get file size validation rule based on document type
     */
    private function getFileSizeRule(string $documentType): string
    {
        $maxSizeKB = match($documentType) {
            'photo' => 2048, // 2MB for photos
            default => 5120  // 5MB for other documents
        };
        
        return "max:{$maxSizeKB}";
    }

    /**
     * Get MIME type validation rule based on document type
     */
    private function getMimeTypeRule(string $documentType): string
    {
        $allowedTypes = match($documentType) {
            'photo' => 'jpg,jpeg,png',
            'identity', 'education', 'cv' => 'jpg,jpeg,png,pdf,doc,docx',
            default => 'jpg,jpeg,png,pdf,doc,docx'
        };
        
        return "mimes:{$allowedTypes}";
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'document.required' => 'Please select a file to upload.',
            'document.file' => 'The uploaded file is not valid.',
            'document.max' => 'The file size exceeds the maximum allowed limit.',
            'document.mimes' => 'The file type is not supported for this document type.',
            'document_type.required' => 'Document type is required.',
            'document_type.in' => 'Invalid document type selected.',
            'sub_type.required' => 'Please specify the document sub-type.',
        ];
    }

    /**
     * Get custom attributes for validation errors
     */
    public function attributes(): array
    {
        return [
            'document' => 'file',
            'document_type' => 'document type',
            'sub_type' => 'document sub-type',
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Clean the document type and sub_type if needed
        if ($this->has('document_type')) {
            $this->merge([
                'document_type' => strtolower(trim($this->document_type))
            ]);
        }
        
        if ($this->has('sub_type')) {
            $this->merge([
                'sub_type' => $this->sub_type ? strtolower(trim($this->sub_type)) : null
            ]);
        }
    }
}