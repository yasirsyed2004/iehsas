<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Upload - IEHSAS Enrollment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .progress-bar {
            transition: width 0.3s ease;
        }
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        .accordion-content.active {
            max-height: 2000px;
            transition: max-height 0.5s ease-in;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 0%, transparent 50%);"></div>

    <div class="relative min-h-screen p-4">
        <!-- Header -->
        <div class="text-center mb-8 pt-8">
            <h1 class="text-4xl font-bold text-white mb-2">IEHSAS Enrollment</h1>
            <p class="text-blue-200 text-lg">Document Submission</p>
        </div>

        <!-- Student Info -->
        <div class="max-w-6xl mx-auto mb-8">
            <div class="glass-effect rounded-xl p-6">
                <h2 class="text-white text-xl font-semibold mb-4">Student Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-300">Name:</span> <span class="text-white font-medium">{{ $student->full_name }}</span></div>
                    <div><span class="text-gray-300">{{ $student->id_type === 'cnic' ? 'CNIC' : ($student->id_type === 'passport' ? 'Passport' : 'Form-B') }}:</span> <span class="text-white font-medium">{{ $student->formatted_id }}</span></div>
                    <div><span class="text-gray-300">Email:</span> <span class="text-white font-medium">{{ $student->email }}</span></div>
                    <div><span class="text-gray-300">Contact:</span> <span class="text-white font-medium">{{ $student->contact_number }}</span></div>
                </div>
            </div>
        </div>

        <!-- Selected Courses Section -->
        @php
            $validCourses = $student->selectedCourses->filter(fn($s) => $s->course !== null);
        @endphp
        
        @if($validCourses->count() > 0)
        <div class="max-w-6xl mx-auto mb-8">
            <div class="glass-effect rounded-xl p-6">
                <h2 class="text-white text-xl font-semibold mb-4">Selected Courses</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($validCourses as $selection)
                        <div class="bg-white bg-opacity-5 rounded-lg p-4 border border-white border-opacity-10 hover:bg-opacity-10 transition-all duration-300">
                            <div class="flex items-center mb-2">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-book text-white"></i>
                                </div>
                                <h3 class="text-white font-medium text-sm">{{ $selection->course->title }}</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $selection->course->level === 'beginner' ? 'bg-green-500' : ($selection->course->level === 'intermediate' ? 'bg-yellow-500' : 'bg-red-500') }} text-white">
                                    {{ ucfirst($selection->course->level ?? 'Beginner') }}
                                </span>
                                <span class="text-gray-300 text-xs">
                                    {{ $selection->course->start_date ? $selection->course->start_date->format('M d, Y') : 'TBA' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 p-3 bg-blue-500 bg-opacity-20 border border-blue-400 border-opacity-30 rounded-lg">
                    <p class="text-blue-200 text-sm">
                        <i class="fas fa-info-circle mr-2"></i>
                        You have selected <strong>{{ $validCourses->count() }}</strong> course(s) during registration
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <div class="max-w-6xl mx-auto" x-data="documentUpload()">
            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="glass-effect rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-white text-lg font-semibold">Upload Progress</h3>
                        <span class="text-blue-300 text-sm" x-text="`${uploadedCount}/${requiredCount} documents uploaded`"></span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full progress-bar" 
                             :style="`width: ${progress}%`"></div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-500 bg-opacity-20 border border-green-500 rounded-lg">
                    <p class="text-green-300 text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-500 bg-opacity-20 border border-red-500 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-300 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Document Upload Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                @foreach($documentStructure as $type => $structure)
                    <div class="glass-effect rounded-xl p-6" x-data="{ uploading: false, showOptional: false }">
                        <!-- Card Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <i class="fas {{ $type === 'identity' ? 'fa-id-card' : ($type === 'photo' ? 'fa-camera' : ($type === 'education' ? 'fa-certificate' : 'fa-file-alt')) }} text-blue-400 mr-2 text-lg"></i>
                                <h3 class="text-white font-semibold">{{ $structure['label'] }}</h3>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium text-yellow-600 bg-yellow-100">
                                Required
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div>
                            @if($structure['subtypes'])
                                @php
                                    $requiredSubtypes = collect($structure['subtypes'])->filter(fn($s) => $s['required']);
                                    $optionalSubtypes = collect($structure['subtypes'])->filter(fn($s) => !$s['required']);
                                @endphp

                                <!-- Required Documents -->
                                <div class="space-y-4 mb-4">
                                    @foreach($requiredSubtypes as $subType => $subInfo)
                                        @php
                                            $uploadedDoc = $documents[$type][$subType] ?? null;
                                        @endphp

                                        <div class="pb-3 last:pb-0">
                                            <label class="block text-white font-medium text-sm mb-3">
                                                <i class="fas fa-file-upload text-blue-400 mr-2"></i>
                                                {{ $subInfo['label'] }}
                                                <span class="text-red-400 text-lg">*</span>
                                            </label>

                                            @if(!$uploadedDoc)
                                                <!-- Upload Area -->
                                                <div class="border-2 border-dashed border-gray-500 rounded-lg p-3 text-center hover:border-blue-400 transition-colors duration-200 cursor-pointer"
                                                     @dragover.prevent
                                                     @dragenter.prevent
                                                     @drop.prevent="handleDrop($event, '{{ $type }}', '{{ $subType }}')"
                                                     x-show="!uploading"
                                                     onclick="document.getElementById('file_{{ $type }}_{{ $subType }}').click()">
                                                    <div class="text-gray-400 mb-2">
                                                        <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-4l4 4m-16-8V16a4 4 0 014-4h8a4 4 0 014 4v4" />
                                                        </svg>
                                                    </div>
                                                    <p class="text-white text-xs mb-1">Click to browse</p>
                                                    <p class="text-gray-400 text-xs">JPG, PNG, PDF, DOC (Max 5MB)</p>
                                                    <input type="file" 
                                                           id="file_{{ $type }}_{{ $subType }}"
                                                           accept="image/*,.pdf,.doc,.docx"
                                                           @change="uploadFile($event, '{{ $type }}', '{{ $subType }}')"
                                                           class="hidden">
                                                </div>
                                            @else
                                                <!-- Uploaded Document Display -->
                                                <div class="bg-green-500 bg-opacity-20 border border-green-500 rounded-lg p-2">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-check-circle text-green-400 text-lg"></i>
                                                            <div>
                                                                <p class="text-green-300 font-medium text-xs">{{ $uploadedDoc->original_filename }}</p>
                                                                <p class="text-green-200 text-xs">{{ $uploadedDoc->formatted_file_size }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="flex space-x-2">
                                                            <a href="{{ $uploadedDoc->file_url }}" 
                                                               target="_blank"
                                                               class="text-blue-400 hover:text-blue-300 text-xs underline">
                                                                View
                                                            </a>
                                                            @if($uploadedDoc->status === 'pending' || $uploadedDoc->status === 'rejected')
                                                                <button @click="deleteDocument({{ $uploadedDoc->id }})"
                                                                        class="text-red-400 hover:text-red-300 text-xs underline">
                                                                    Delete
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    @if($uploadedDoc->status === 'rejected' && $uploadedDoc->admin_comments)
                                                        <div class="mt-2 p-2 bg-red-500 bg-opacity-20 border border-red-500 rounded text-xs">
                                                            <p class="text-red-300 font-medium">Reason: {{ $uploadedDoc->admin_comments }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Optional Documents Accordion -->
                                @if($optionalSubtypes->count() > 0)
                                    <div class="border-t border-gray-600 border-opacity-30 pt-4">
                                        <button @click="showOptional = !showOptional" 
                                                type="button"
                                                class="w-full flex items-center justify-between text-left text-blue-300 hover:text-blue-200 transition-colors">
                                            <span class="text-sm font-medium">
                                                <i class="fas fa-plus-circle mr-2"></i>
                                                Add Optional Documents ({{ $optionalSubtypes->count() }})
                                            </span>
                                            <i class="fas fa-chevron-down transition-transform duration-300" 
                                               :class="showOptional ? 'rotate-180' : ''"></i>
                                        </button>
                                        
                                        <div x-show="showOptional" 
                                             x-collapse
                                             class="mt-4 space-y-4">
                                            @foreach($optionalSubtypes as $subType => $subInfo)
                                                @php
                                                    $uploadedDoc = $documents[$type][$subType] ?? null;
                                                @endphp

                                                <div class="pb-3 last:pb-0">
                                                    <label class="block text-white font-medium text-sm mb-3">
                                                        <i class="fas fa-file-upload text-blue-400 mr-2"></i>
                                                        {{ $subInfo['label'] }}
                                                        <span class="text-gray-400 text-xs ml-1">(Optional)</span>
                                                    </label>

                                                    @if(!$uploadedDoc)
                                                        <!-- Upload Area -->
                                                        <div class="border-2 border-dashed border-gray-500 rounded-lg p-3 text-center hover:border-blue-400 transition-colors duration-200 cursor-pointer"
                                                             @dragover.prevent
                                                             @dragenter.prevent
                                                             @drop.prevent="handleDrop($event, '{{ $type }}', '{{ $subType }}')"
                                                             x-show="!uploading"
                                                             onclick="document.getElementById('file_{{ $type }}_{{ $subType }}').click()">
                                                            <div class="text-gray-400 mb-2">
                                                                <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-4l4 4m-16-8V16a4 4 0 014-4h8a4 4 0 014 4v4" />
                                                                </svg>
                                                            </div>
                                                            <p class="text-white text-xs mb-1">Click to browse</p>
                                                            <p class="text-gray-400 text-xs">JPG, PNG, PDF, DOC (Max 5MB)</p>
                                                            <input type="file" 
                                                                   id="file_{{ $type }}_{{ $subType }}"
                                                                   accept="image/*,.pdf,.doc,.docx"
                                                                   @change="uploadFile($event, '{{ $type }}', '{{ $subType }}')"
                                                                   class="hidden">
                                                        </div>
                                                    @else
                                                        <!-- Uploaded Document Display -->
                                                        <div class="bg-green-500 bg-opacity-20 border border-green-500 rounded-lg p-2">
                                                            <div class="flex items-center justify-between">
                                                                <div class="flex items-center space-x-2">
                                                                    <i class="fas fa-check-circle text-green-400 text-lg"></i>
                                                                    <div>
                                                                        <p class="text-green-300 font-medium text-xs">{{ $uploadedDoc->original_filename }}</p>
                                                                        <p class="text-green-200 text-xs">{{ $uploadedDoc->formatted_file_size }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="flex space-x-2">
                                                                    <a href="{{ $uploadedDoc->file_url }}" 
                                                                       target="_blank"
                                                                       class="text-blue-400 hover:text-blue-300 text-xs underline">
                                                                        View
                                                                    </a>
                                                                    @if($uploadedDoc->status === 'pending' || $uploadedDoc->status === 'rejected')
                                                                        <button @click="deleteDocument({{ $uploadedDoc->id }})"
                                                                                class="text-red-400 hover:text-red-300 text-xs underline">
                                                                            Delete
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                            @if($uploadedDoc->status === 'rejected' && $uploadedDoc->admin_comments)
                                                                <div class="mt-2 p-2 bg-red-500 bg-opacity-20 border border-red-500 rounded text-xs">
                                                                    <p class="text-red-300 font-medium">Reason: {{ $uploadedDoc->admin_comments }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                <!-- Single upload (for photo) -->
                                @php
                                    $uploadedDoc = $documents[$type]['main'] ?? null;
                                @endphp

                                @if(!$uploadedDoc)
                                    <!-- Upload Area -->
                                    <div class="border-2 border-dashed border-gray-500 rounded-lg p-6 text-center hover:border-blue-400 transition-colors duration-200 cursor-pointer"
                                         @dragover.prevent
                                         @dragenter.prevent
                                         @drop.prevent="handleDrop($event, '{{ $type }}', null)"
                                         x-show="!uploading"
                                         onclick="document.getElementById('file_{{ $type }}').click()">
                                        <div class="text-gray-400 mb-4">
                                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-4l4 4m-16-8V16a4 4 0 014-4h8a4 4 0 014 4v4" />
                                            </svg>
                                        </div>
                                        <p class="text-white mb-2">Drop file here or click to browse</p>
                                        <p class="text-gray-400 text-sm mb-4">
                                            JPG, PNG (Max 2MB)
                                        </p>
                                        <input type="file" 
                                               id="file_{{ $type }}"
                                               accept="image/*"
                                               @change="uploadFile($event, '{{ $type }}', null)"
                                               class="hidden">
                                        <button type="button" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                            Choose File
                                        </button>
                                    </div>

                                    <!-- Upload Progress -->
                                    <div x-show="uploading" class="text-center py-6">
                                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mb-2"></div>
                                        <p class="text-blue-300 text-sm">Uploading...</p>
                                    </div>
                                @else
                                    <!-- Uploaded Document Display -->
                                    <div class="bg-green-500 bg-opacity-20 border border-green-500 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-green-300 font-medium">{{ $uploadedDoc->original_filename }}</p>
                                                    <div class="flex items-center space-x-3">
                                                        <p class="text-green-200 text-sm">{{ $uploadedDoc->formatted_file_size }}</p>
                                                        <span class="px-2 py-1 text-xs font-bold rounded-full {{ $uploadedDoc->status === 'approved' ? 'bg-green-500 text-white' : ($uploadedDoc->status === 'rejected' ? 'bg-red-500 text-white' : 'bg-yellow-500 text-white') }}">
                                                            {{ $uploadedDoc->status_label }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="{{ $uploadedDoc->file_url }}" 
                                                   target="_blank"
                                                   class="text-blue-400 hover:text-blue-300 text-sm underline">
                                                    View
                                                </a>
                                                @if($uploadedDoc->status === 'pending' || $uploadedDoc->status === 'rejected')
                                                    <button @click="deleteDocument({{ $uploadedDoc->id }})"
                                                            class="text-red-400 hover:text-red-300 text-sm underline">
                                                        Delete
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($uploadedDoc->status === 'rejected' && $uploadedDoc->admin_comments)
                                            <div class="mt-3 p-3 bg-red-500 bg-opacity-20 border border-red-500 rounded-lg">
                                                <p class="text-red-300 text-sm font-medium mb-1">Rejection Reason:</p>
                                                <p class="text-red-200 text-sm">{{ $uploadedDoc->admin_comments }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Submit Button -->
            <div class="text-center mb-8">
                <form method="POST" action="{{ route('enrollment.documents.submit') }}">
                    @csrf
                    <button type="submit" 
                            :disabled="!canSubmit"
                            :class="canSubmit ? 'bg-gradient-to-r from-green-500 to-blue-600 hover:from-green-600 hover:to-blue-700' : 'bg-gray-500 cursor-not-allowed'"
                            class="px-8 py-4 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-transparent">
                        <span x-show="canSubmit">Submit Enrollment Form</span>
                        <span x-show="!canSubmit">Upload All Documents to Continue</span>
                    </button>
                </form>
                <p class="text-gray-300 text-sm mt-2" x-show="!canSubmit">
                    Please upload all <span class="font-bold" x-text="requiredCount"></span> required documents before submitting your enrollment.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('documentUpload', () => ({
                uploadedDocuments: @json($documents),
                requiredCount: 5,

                get uploadedCount() {
                    let count = 0;
                    // Required documents only for count
                    if (this.uploadedDocuments.identity?.cnic_front) count++;
                    if (this.uploadedDocuments.identity?.cnic_back) count++;
                    if (this.uploadedDocuments.education?.matric) count++;
                    if (this.uploadedDocuments.cv?.cv) count++;
                    if (this.uploadedDocuments.photo?.main) count++;
                    return count;
                },

                get progress() {
                    return Math.round((this.uploadedCount / this.requiredCount) * 100);
                },

                get canSubmit() {
                    return this.uploadedCount >= this.requiredCount;
                },

                async uploadFile(event, documentType, subType) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.uploading = true;

                    const formData = new FormData();
                    formData.append('document', file);
                    formData.append('document_type', documentType);
                    if (subType) {
                        formData.append('sub_type', subType);
                    }

                    try {
                        const response = await fetch('{{ route("enrollment.documents.upload") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            window.location.reload();
                        } else {
                            alert('Upload failed: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Upload error:', error);
                        alert('Upload failed: ' + error.message);
                    } finally {
                        this.uploading = false;
                    }
                },

                handleDrop(event, documentType, subType) {
                    const files = event.dataTransfer.files;
                    if (files.length > 0) {
                        const fakeEvent = { target: { files: [files[0]] } };
                        this.uploadFile(fakeEvent, documentType, subType);
                    }
                },

                async deleteDocument(documentId) {
                    if (!confirm('Are you sure you want to delete this document?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/enrollment/documents/${documentId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            window.location.reload();
                        } else {
                            alert('Delete failed: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Delete error:', error);
                        alert('Delete failed: ' + error.message);
                    }
                }
            }))
        });
    </script>
</body>
</html>