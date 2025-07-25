{{-- File: resources/views/admin/entry-tests/create.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Entry Test - Admin LMS</title>
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
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .btn {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .invalid-feedback {
            font-size: 0.875rem;
        }
        .info-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .preview-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
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
                <a class="nav-link active" href="{{ route('admin.entry-tests.index') }}">
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
                <a class="nav-link" href="#" onclick="alert('Coming Soon!')">
                    <i class="fas fa-book me-2"></i> E-Learning
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="alert('Coming Soon!')">
                    <i class="fas fa-chart-bar me-2"></i> Reports
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
                <h2><i class="fas fa-plus-circle me-2"></i>Create New Entry Test</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.entry-tests.index') }}">Entry Tests</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.entry-tests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Info Box -->
        <div class="info-box">
            <h5><i class="fas fa-info-circle me-2"></i>Creating Entry Test</h5>
            <p class="mb-0">Fill out the form below to create a new entry test. Make sure to review all settings before saving. You can add questions after creating the test.</p>
        </div>

        <!-- Create Form -->
        <div class="form-card">
            <form action="{{ route('admin.entry-tests.store') }}" method="POST" id="createForm">
                @csrf
                
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h5 class="text-primary"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                            <hr>
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <i class="fas fa-heading me-1"></i>Test Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   placeholder="e.g., General Knowledge Entry Test"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Description <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4"
                                      placeholder="Describe the entry test, its purpose, and what students should expect..."
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Test Settings -->
                        <div class="mb-4 mt-5">
                            <h5 class="text-primary"><i class="fas fa-cogs me-2"></i>Test Settings</h5>
                            <hr>
                        </div>

                        <div class="row">
                            <!-- Duration -->
                            <div class="col-md-6 mb-3">
                                <label for="duration_minutes" class="form-label">
                                    <i class="fas fa-clock me-1"></i>Duration (Minutes) <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('duration_minutes') is-invalid @enderror" 
                                       id="duration_minutes" 
                                       name="duration_minutes" 
                                       value="{{ old('duration_minutes', 60) }}"
                                       min="1" 
                                       max="180" 
                                       required>
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Recommended: 60-90 minutes</div>
                            </div>

                            <!-- Total Questions -->
                            <div class="col-md-6 mb-3">
                                <label for="total_questions" class="form-label">
                                    <i class="fas fa-list-ol me-1"></i>Total Questions <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('total_questions') is-invalid @enderror" 
                                       id="total_questions" 
                                       name="total_questions" 
                                       value="{{ old('total_questions', 20) }}"
                                       min="1" 
                                       max="100" 
                                       required>
                                @error('total_questions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">You'll add questions after creating the test</div>
                            </div>
                        </div>

                        <!-- Passing Score -->
                        <div class="mb-3">
                            <label for="passing_score" class="form-label">
                                <i class="fas fa-percentage me-1"></i>Passing Score (%) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('passing_score') is-invalid @enderror" 
                                   id="passing_score" 
                                   name="passing_score" 
                                   value="{{ old('passing_score', 60) }}"
                                   min="0" 
                                   max="100" 
                                   step="0.1" 
                                   required>
                            @error('passing_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Students need this percentage to pass</div>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="fas fa-toggle-on me-1"></i>Activate Test Immediately
                                </label>
                            </div>
                            <div class="form-text">Only one test can be active at a time</div>
                        </div>
                    </div>

                    <!-- Right Column - Preview -->
                    <div class="col-lg-4">
                        <div class="preview-section">
                            <h5 class="text-success"><i class="fas fa-eye me-2"></i>Preview</h5>
                            <hr>
                            
                            <div class="preview-content">
                                <h6 id="preview-title" class="text-primary">Test Title Will Appear Here</h6>
                                <p id="preview-description" class="text-muted small">Test description will appear here...</p>
                                
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <small><i class="fas fa-clock me-1"></i>Duration:</small>
                                        <small id="preview-duration" class="fw-bold">60 minutes</small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small><i class="fas fa-list me-1"></i>Questions:</small>
                                        <small id="preview-questions" class="fw-bold">20</small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small><i class="fas fa-percentage me-1"></i>Pass Score:</small>
                                        <small id="preview-passing" class="fw-bold">60%</small>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small><i class="fas fa-toggle-on me-1"></i>Status:</small>
                                        <small id="preview-status" class="fw-bold text-danger">Inactive</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-light rounded">
                                <h6 class="text-info"><i class="fas fa-lightbulb me-1"></i>Tips</h6>
                                <ul class="small mb-0">
                                    <li>Choose a clear, descriptive title</li>
                                    <li>Set appropriate time limits</li>
                                    <li>Consider difficulty when setting pass score</li>
                                    <li>Test thoroughly before activation</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.entry-tests.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <div>
                            <button type="submit" name="action" value="save" class="btn btn-primary me-2">
                                <i class="fas fa-save me-2"></i>Create Test
                            </button>
                            <button type="submit" name="action" value="save_and_add_questions" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Create & Add Questions
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live Preview Updates
        document.getElementById('title').addEventListener('input', function() {
            const previewTitle = document.getElementById('preview-title');
            previewTitle.textContent = this.value || 'Test Title Will Appear Here';
        });

        document.getElementById('description').addEventListener('input', function() {
            const previewDesc = document.getElementById('preview-description');
            previewDesc.textContent = this.value || 'Test description will appear here...';
        });

        document.getElementById('duration_minutes').addEventListener('input', function() {
            const previewDuration = document.getElementById('preview-duration');
            previewDuration.textContent = (this.value || '60') + ' minutes';
        });

        document.getElementById('total_questions').addEventListener('input', function() {
            const previewQuestions = document.getElementById('preview-questions');
            previewQuestions.textContent = this.value || '20';
        });

        document.getElementById('passing_score').addEventListener('input', function() {
            const previewPassing = document.getElementById('preview-passing');
            previewPassing.textContent = (this.value || '60') + '%';
        });

        document.getElementById('is_active').addEventListener('change', function() {
            const previewStatus = document.getElementById('preview-status');
            if (this.checked) {
                previewStatus.textContent = 'Active';
                previewStatus.className = 'fw-bold text-success';
            } else {
                previewStatus.textContent = 'Inactive';
                previewStatus.className = 'fw-bold text-danger';
            }
        });

        // Form Validation
        document.getElementById('createForm').addEventListener('submit', function(e) {
            const duration = parseInt(document.getElementById('duration_minutes').value);
            const questions = parseInt(document.getElementById('total_questions').value);
            const passingScore = parseFloat(document.getElementById('passing_score').value);

            if (duration < 1 || duration > 180) {
                e.preventDefault();
                alert('Duration must be between 1 and 180 minutes.');
                return;
            }

            if (questions < 1 || questions > 100) {
                e.preventDefault();
                alert('Total questions must be between 1 and 100.');
                return;
            }

            if (passingScore < 0 || passingScore > 100) {
                e.preventDefault();
                alert('Passing score must be between 0 and 100.');
                return;
            }
        });

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('show')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
    </script>
</body>
</html>