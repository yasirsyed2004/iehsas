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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $documentType = $this->input('document_type');
        
        return [
            'document_type' => 'required|in:identity,education,cv,photo',
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
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Clean the document type if needed
        if ($this->has('document_type')) {
            $this->merge([
                'document_type' => strtolower(trim($this->document_type))
            ]);
        }
    }
}