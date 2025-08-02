{{-- File: resources/views/admin/courses/edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $course->title }} - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            border-radius: 5px;
            margin: 2px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar .nav-link.active {
            background-color: #3498db;
            color: white;
        }
        .user-info {
            color: #ecf0f1;
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .form-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
        .image-preview {
            width: 100%;
            max-width: 200px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
            display: none;
        }
        .image-preview.show {
            display: block;
        }
        .current-image {
            width: 100%;
            max-width: 200px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }
        .learning-outcome-item {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .btn-add-outcome {
            background: #e3f2fd;
            border: 1px dashed #2196f3;
            color: #1976d2;
        }
        .btn-add-outcome:hover {
            background: #bbdefb;
        }
        .status-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="user-info text-center">
            <div class="mb-2">
                @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->avatar)
                    <img src="{{ asset('storage/' . Auth::guard('admin')->user()->avatar) }}" alt="Avatar" class="rounded-circle" width="60" height="60">
                @else
                    <i class="fas fa-user-circle fa-3x"></i>
                @endif
            </div>
            <h6>{{ Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : 'Admin' }}</h6>
            <small>{{ Auth::guard('admin')->check() ? ucfirst(str_replace('_', ' ', Auth::guard('admin')->user()->role ?? 'admin')) : 'Administrator' }}</small>
        </div>
        
        <ul class="nav flex-column p-3">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users me-2"></i> Users Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.entry-tests.index') }}">
                    <i class="fas fa-clipboard-list me-2"></i> Entry Tests
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.questions.index') }}">
                    <i class="fas fa-question-circle me-2"></i> Question Bank
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.student-attempts.index') }}">
                    <i class="fas fa-chart-line me-2"></i> Student Attempts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.courses.index') }}">
                    <i class="fas fa-graduation-cap me-2"></i> Courses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.course-categories.index') }}">
                    <i class="fas fa-tags me-2"></i> Course Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.enrollments.index') }}">
                    <i class="fas fa-user-graduate me-2"></i> Enrollments
                </a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link" href="{{ route('admin.profile') }}">
                    <i class="fas fa-user-cog me-2"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-edit me-2"></i>Edit Course</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course) }}">{{ $course->title }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-eye me-1"></i>View Course
                </a>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Courses
                </a>
            </div>
        </div>

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Course Status Info -->
        <div class="status-info">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-1">{{ $course->title }}</h5>
                    <p class="mb-0 opacity-90">Current Status: 
                        <strong>{{ ucfirst($course->status) }}</strong>
                        @if(!$course->is_active) • <strong>Inactive</strong> @endif
                        @if($course->is_featured) • <strong>Featured</strong> @endif
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <small class="opacity-75">
                        Created: {{ $course->created_at->format('M d, Y') }}<br>
                        Last Updated: {{ $course->updated_at->format('M d, Y H:i') }}
                    </small>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="form-section">
                <h4 class="section-title"><i class="fas fa-info-circle me-2"></i>Basic Information</h4>
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $course->title) }}" required>
                            <div class="form-text">Enter a clear, descriptive title for your course</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="level" class="form-label">Difficulty Level <span class="text-danger">*</span></label>
                            <select class="form-select" id="level" name="level" required>
                                <option value="">Select Level</option>
                                <option value="beginner" {{ old('level', $course->level) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('level', $course->level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('level', $course->level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label">Short Description</label>
                    <textarea class="form-control" id="short_description" name="short_description" rows="2" maxlength="500">{{ old('short_description', $course->short_description) }}</textarea>
                    <div class="form-text">Brief summary that appears in course listings (max 500 characters)</div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Full Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="6" required>{{ old('description', $course->description) }}</textarea>
                    <div class="form-text">Detailed description of what students will learn</div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="instructor_id" class="form-label">Instructor</label>
                            <select class="form-select" id="instructor_id" name="instructor_id">
                                <option value="">Select Instructor</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" {{ old('instructor_id', $course->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Details -->
            <div class="form-section">
                <h4 class="section-title"><i class="fas fa-cog me-2"></i>Course Details</h4>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="duration_hours" class="form-label">Duration (Hours) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="duration_hours" name="duration_hours" value="{{ old('duration_hours', $course->duration_hours) }}" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $course->price) }}" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="discount_price" class="form-label">Discount Price ($)</label>
                            <input type="number" class="form-control" id="discount_price" name="discount_price" value="{{ old('discount_price', $course->discount_price) }}" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="max_students" class="form-label">Max Students</label>
                            <input type="number" class="form-control" id="max_students" name="max_students" value="{{ old('max_students', $course->max_students) }}" min="1">
                            <div class="form-text">Leave empty for unlimited</div>
                        </div>
                    </div>
                </div>

                <!-- Entry Test Requirements -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="requires_entry_test" name="requires_entry_test" value="1" {{ old('requires_entry_test', $course->requires_entry_test) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_entry_test">
                                <strong>Requires Entry Test</strong>
                            </label>
                            <div class="form-text">Students must pass entry test before enrolling</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="min_entry_test_score" class="form-label">Minimum Test Score (%)</label>
                            <input type="number" class="form-control" id="min_entry_test_score" name="min_entry_test_score" value="{{ old('min_entry_test_score', $course->min_entry_test_score) }}" min="0" max="100" {{ $course->requires_entry_test ? '' : 'disabled' }}>
                            <div class="form-text">Required score to enroll (default: 60%)</div>
                        </div>
                    </div>
                </div>

                <!-- Course Features -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="has_certificate" name="has_certificate" value="1" {{ old('has_certificate', $course->has_certificate) ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_certificate">
                                <strong>Provides Certificate</strong>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $course->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                <strong>Featured Course</strong>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>Active Course</strong>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Learning Outcomes -->
            <div class="form-section">
                <h4 class="section-title"><i class="fas fa-bullseye me-2"></i>Learning Outcomes</h4>
                <p class="text-muted mb-3">What will students learn from this course?</p>
                
                <div id="learning-outcomes-container">
                    @if(old('learning_outcomes', $course->learning_outcomes))
                        @foreach(old('learning_outcomes', $course->learning_outcomes) as $index => $outcome)
                        <div class="learning-outcome-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="learning_outcomes[]" value="{{ $outcome }}" placeholder="Enter learning outcome...">
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm ms-2" onclick="removeOutcome(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="learning-outcome-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="learning_outcomes[]" placeholder="Enter learning outcome...">
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm ms-2" onclick="removeOutcome(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
                
                <button type="button" class="btn btn-add-outcome" onclick="addOutcome()">
                    <i class="fas fa-plus me-2"></i>Add Learning Outcome
                </button>
            </div>

            <!-- Requirements -->
            <div class="form-section">
                <h4 class="section-title"><i class="fas fa-list-check me-2"></i>Requirements</h4>
                <div class="mb-3">
                    <label for="requirements" class="form-label">Course Requirements</label>
                    <textarea class="form-control" id="requirements" name="requirements" rows="4">{{ old('requirements', $course->requirements) }}</textarea>
                    <div class="form-text">What do students need before taking this course? (prior knowledge, software, etc.)</div>
                </div>
            </div>

            <!-- Course Images -->
            <div class="form-section">
                <h4 class="section-title"><i class="fas fa-images me-2"></i>Course Images</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="thumbnail" class="form-label">Course Thumbnail</label>
                            @if($course->thumbnail)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Current thumbnail" class="current-image">
                                    <div class="form-text">Current thumbnail</div>
                                </div>
                            @endif
                            <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*" onchange="previewImage(this, 'thumbnail-preview')">
                            <div class="form-text">Recommended: 300x200px, max 2MB (leave empty to keep current)</div>
                            <img id="thumbnail-preview" class="image-preview mt-2">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="banner_image" class="form-label">Banner Image</label>
                            @if($course->banner_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $course->banner_image) }}" alt="Current banner" class="current-image">
                                    <div class="form-text">Current banner</div>
                                </div>
                            @endif
                            <input type="file" class="form-control" id="banner_image" name="banner_image" accept="image/*" onchange="previewImage(this, 'banner-preview')">
                            <div class="form-text">Recommended: 1200x400px, max 5MB (leave empty to keep current)</div>
                            <img id="banner-preview" class="image-preview mt-2">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Status -->
            <div class="form-section">
                <h4 class="section-title"><i class="fas fa-toggle-on me-2"></i>Course Status</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Publication Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $course->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $course->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            <div class="form-text">Published courses are visible to students</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if($course->published_at)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Published:</strong> {{ $course->published_at->format('M d, Y H:i') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Changes will be saved immediately. Students enrolled in this course will see updates.
                        </small>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Course
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Entry test requirement toggle
        document.getElementById('requires_entry_test').addEventListener('change', function() {
            const scoreInput = document.getElementById('min_entry_test_score');
            scoreInput.disabled = !this.checked;
            if (!this.checked) {
                scoreInput.value = '';
            } else if (!scoreInput.value) {
                scoreInput.value = 60;
            }
        });

        // Learning outcomes management
        function addOutcome() {
            const container = document.getElementById('learning-outcomes-container');
            const outcomeHtml = `
                <div class="learning-outcome-item">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <input type="text" class="form-control" name="learning_outcomes[]" placeholder="Enter learning outcome...">
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm ms-2" onclick="removeOutcome(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', outcomeHtml);
        }

        function removeOutcome(button) {
            const container = document.getElementById('learning-outcomes-container');
            if (container.children.length > 1) {
                button.closest('.learning-outcome-item').remove();
            }
        }

        // Image preview
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('show');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Price validation
        document.getElementById('discount_price').addEventListener('input', function() {
            const price = parseFloat(document.getElementById('price').value) || 0;
            const discountPrice = parseFloat(this.value) || 0;
            
            if (discountPrice >= price && price > 0) {
                this.setCustomValidity('Discount price must be less than regular price');
            } else {
                this.setCustomValidity('');
            }
        });

        // Set initial state for entry test score field
        document.addEventListener('DOMContentLoaded', function() {
            const requiresTest = document.getElementById('requires_entry_test');
            const scoreInput = document.getElementById('min_entry_test_score');
            scoreInput.disabled = !requiresTest.checked;
        });
    </script>
</body>
</html>