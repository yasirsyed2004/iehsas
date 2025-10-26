<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Upload - IEHSAS Enrollment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
        <div class="max-w-4xl mx-auto mb-8">
            <div class="glass-effect rounded-xl p-6">
                <h2 class="text-white text-xl font-semibold mb-4">Student Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-300">Name:</span> <span class="text-white font-medium">{{ $student->full_name }}</span></div>
                    <div><span class="text-gray-300">{{ $student->id_type_label }}:</span> <span class="text-white font-medium">{{ $student->formatted_id }}</span></div>
                    <div><span class="text-gray-300">Email:</span> <span class="text-white font-medium">{{ $student->email }}</span></div>
                    <div><span class="text-gray-300">Contact:</span> <span class="text-white font-medium">{{ $student->contact_number }}</span></div>
                </div>
            </div>
        </div>

        <!-- Selected Courses Section -->
@if($student->selectedCourses && $student->selectedCourses->count() > 0)
<div class="max-w-4xl mx-auto mb-8">
    <div class="glass-effect rounded-xl p-6">
        <h2 class="text-white text-xl font-semibold mb-4">Selected Courses</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($student->selectedCourses as $course)
                <div class="bg-white bg-opacity-5 rounded-lg p-4 border border-white border-opacity-10 hover:bg-opacity-10 transition-all duration-300">
                    <div class="flex items-center mb-2">
                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-book text-white"></i>
                        </div>
                        <h3 class="text-white font-medium text-sm">{{ $course->title }}</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $course->level === 'beginner' ? 'bg-green-500' : ($course->level === 'intermediate' ? 'bg-yellow-500' : 'bg-red-500') }} text-white">
                            {{ ucfirst($course->level) }}
                        </span>
                        @if($course->pivot && $course->pivot->selected_at)
                            <span class="text-gray-300 text-xs">
                                {{ \Carbon\Carbon::parse($course->pivot->selected_at)->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 p-3 bg-blue-500 bg-opacity-20 border border-blue-400 border-opacity-30 rounded-lg">
            <p class="text-blue-200 text-sm">
                <i class="fas fa-info-circle mr-2"></i>
                You have selected <strong>{{ $student->selectedCourses->count() }}</strong> course(s) during registration
            </p>
        </div>
    </div>
</div>
@endif

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto" x-data="documentUpload">
            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="glass-effect rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-white text-lg font-semibold">Upload Progress</h3>
                        <span class="text-blue-300 text-sm" x-text="`${uploadedCount}/4 documents uploaded`"></span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full progress-bar" 
                             :style="`width: ${(uploadedCount / 4) * 100}%`"></div>
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
                @foreach ($documentTypes as $type => $label)
                    <div class="glass-effect rounded-xl p-6" x-data="{ uploading: false }">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-white font-semibold">{{ $label }}</h3>
                            @if (isset($documents[$type]))
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $documents[$type]->status_color }}">
                                    {{ $documents[$type]->status_label }}
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-medium text-yellow-600 bg-yellow-100">
                                    Required
                                </span>
                            @endif
                        </div>

                        <!-- Upload Area -->
                        @if (!isset($documents[$type]) || $documents[$type]->status === 'rejected')
                            <div class="border-2 border-dashed border-gray-500 rounded-lg p-6 text-center hover:border-blue-400 transition-colors duration-200"
                                 @dragover.prevent
                                 @dragenter.prevent
                                 @drop.prevent="handleDrop($event, '{{ $type }}')"
                                 x-show="!uploading">
                                <div class="text-gray-400 mb-4">
                                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-4l4 4m-16-8V16a4 4 0 014-4h8a4 4 0 014 4v4" />
                                    </svg>
                                </div>
                                <p class="text-white mb-2">Drop file here or click to browse</p>
                                <p class="text-gray-400 text-sm mb-4">
                                    @if ($type === 'photo')
                                        JPG, PNG (Max 2MB)
                                    @else
                                        JPG, PNG, PDF, DOC, DOCX (Max 5MB)
                                    @endif
                                </p>
                                <input type="file" 
                                       accept="{{ $type === 'photo' ? 'image/*' : 'image/*,.pdf,.doc,.docx' }}"
                                       @change="uploadFile($event, '{{ $type }}')"
                                       class="hidden">
                                <button type="button" 
                                        @click="$event.target.previousElementSibling.click()"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                    Choose File
                                </button>
                            </div>

                            <!-- Upload Progress -->
                            <div x-show="uploading" class="text-center py-6">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mb-2"></div>
                                <p class="text-blue-300 text-sm">Uploading...</p>
                            </div>

                            @if (isset($documents[$type]) && $documents[$type]->status === 'rejected')
                                <div class="mt-4 p-3 bg-red-500 bg-opacity-20 border border-red-500 rounded-lg">
                                    <p class="text-red-300 text-sm font-medium mb-1">Rejection Reason:</p>
                                    <p class="text-red-200 text-sm">{{ $documents[$type]->admin_comments }}</p>
                                </div>
                            @endif
                        @else
                            <!-- Uploaded Document Display -->
                            <div class="bg-green-500 bg-opacity-20 border border-green-500 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            @if ($documents[$type]->is_image)
                                                <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            @else
                                                <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-green-300 font-medium">{{ $documents[$type]->original_filename }}</p>
                                            <p class="text-green-200 text-sm">{{ $documents[$type]->formatted_file_size }}</p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ $documents[$type]->file_url }}" 
                                           target="_blank"
                                           class="text-blue-400 hover:text-blue-300 text-sm underline">
                                            View
                                        </a>
                                        @if ($documents[$type]->status === 'pending')
                                            <button @click="deleteDocument({{ $documents[$type]->id }})"
                                                    class="text-red-400 hover:text-red-300 text-sm underline">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Submit Button -->
            <div class="text-center mb-8">
                <form method="POST" action="{{ route('enrollment.documents.submit') }}">
                    @csrf
                    <button type="submit" 
                            :disabled="uploadedCount < 4"
                            :class="uploadedCount >= 4 ? 'bg-gradient-to-r from-green-500 to-blue-600 hover:from-green-600 hover:to-blue-700' : 'bg-gray-500 cursor-not-allowed'"
                            class="px-8 py-4 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-transparent">
                        <span x-show="uploadedCount >= 4">Submit Enrollment Form</span>
                        <span x-show="uploadedCount < 4">Upload All Documents to Continue</span>
                    </button>
                </form>
                <p class="text-gray-300 text-sm mt-2" x-show="uploadedCount < 4">
                    Please upload all 4 required documents before submitting your enrollment.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('documentUpload', () => ({
                uploadedDocuments: @json($documents->keys()),

                get uploadedCount() {
                    return this.uploadedDocuments.length;
                },

                async uploadFile(event, documentType) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('document', file);
                    formData.append('document_type', documentType);

                    const uploading = this.setUploading(documentType, true);

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
                            // Add to uploaded documents if not already there
                            if (!this.uploadedDocuments.includes(documentType)) {
                                this.uploadedDocuments.push(documentType);
                            }
                            // Reload page to show updated document
                            window.location.reload();
                        } else {
                            alert('Upload failed: ' + result.message);
                        }
                    } catch (error) {
                        alert('Upload failed: ' + error.message);
                    } finally {
                        this.setUploading(documentType, false);
                    }
                },

                handleDrop(event, documentType) {
                    const files = event.dataTransfer.files;
                    if (files.length > 0) {
                        const fakeEvent = { target: { files: [files[0]] } };
                        this.uploadFile(fakeEvent, documentType);
                    }
                },

                async deleteDocument(documentId) {
                    if (!confirm('Are you sure you want to delete this document?')) return;

                    try {
                        const response = await fetch(`/enrollment/documents/${documentId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            window.location.reload();
                        } else {
                            alert('Delete failed: ' + result.message);
                        }
                    } catch (error) {
                        alert('Delete failed: ' + error.message);
                    }
                },

                setUploading(documentType, state) {
                    // This is a simple way to handle uploading state
                    // In a real implementation, you might want to track this per document type
                    return state;
                }
            }))
        })
    </script>
</body>
</html>