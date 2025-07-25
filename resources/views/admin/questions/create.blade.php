{{-- File: resources/views/admin/questions/create.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Question - Admin LMS</title>
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
        .option-group {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .option-group:hover {
            border-color: #007bff;
            background-color: #f8f9ff;
        }
        .option-group.correct {
            border-color: #28a745;
            background-color: #f8fff9;
        }
        .remove-option {
            cursor: pointer;
            color: #dc3545;
            font-size: 1.2rem;
        }
        .remove-option:hover {
            color: #c82333;
        }
        .question-preview {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-top: 15px;
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
                <a class="nav-link active" href="{{ route('admin.questions.index') }}">
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
                <h2><i class="fas fa-plus-circle me-2"></i>Create New Question</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">Question Bank</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Questions
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
            <h5><i class="fas fa-info-circle me-2"></i>Creating New Question</h5>
            <p class="mb-0">Fill out the form below to add a new question to your question bank. Make sure to select the correct answer and assign appropriate marks.</p>
        </div>

        <!-- Create Form -->
        <div class="form-card">
            <form action="{{ route('admin.questions.store') }}" method="POST" id="createForm">
                @csrf
                
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Entry Test Selection -->
                        <div class="mb-4">
                            <h5 class="text-primary"><i class="fas fa-clipboard-list me-2"></i>Test Assignment</h5>
                            <hr>
                        </div>

                        <div class="mb-3">
                            <label for="entry_test_id" class="form-label">
                                <i class="fas fa-list-alt me-1"></i>Entry Test <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('entry_test_id') is-invalid @enderror" 
                                    id="entry_test_id" 
                                    name="entry_test_id" 
                                    required>
                                <option value="">Select Entry Test</option>
                                @foreach($entryTests as $test)
                                    <option value="{{ $test->id }}" 
                                            {{ (old('entry_test_id', request('entry_test')) == $test->id) ? 'selected' : '' }}>
                                        {{ $test->title }}
                                        @if(!$test->is_active)
                                            (Inactive)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('entry_test_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Choose which entry test this question belongs to</div>
                        </div>

                        <!-- Question Details -->
                        <div class="mb-4 mt-5">
                            <h5 class="text-primary"><i class="fas fa-question-circle me-2"></i>Question Details</h5>
                            <hr>
                        </div>

                        <!-- Question Text -->
                        <div class="mb-3">
                            <label for="question_text" class="form-label">
                                <i class="fas fa-edit me-1"></i>Question Text <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('question_text') is-invalid @enderror" 
                                      id="question_text" 
                                      name="question_text" 
                                      rows="4"
                                      placeholder="Enter your question here..."
                                      required>{{ old('question_text') }}</textarea>
                            @error('question_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Question Type -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="question_type" class="form-label">
                                    <i class="fas fa-tags me-1"></i>Question Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('question_type') is-invalid @enderror" 
                                        id="question_type" 
                                        name="question_type" 
                                        required>
                                    <option value="">Select Type</option>
                                    <option value="mcq" {{ old('question_type') == 'mcq' ? 'selected' : '' }}>
                                        Multiple Choice (MCQ)
                                    </option>
                                    <option value="true_false" {{ old('question_type') == 'true_false' ? 'selected' : '' }}>
                                        True/False
                                    </option>
                                </select>
                                @error('question_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Marks -->
                            <div class="col-md-6 mb-3">
                                <label for="marks" class="form-label">
                                    <i class="fas fa-star me-1"></i>Marks <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('marks') is-invalid @enderror" 
                                       id="marks" 
                                       name="marks" 
                                       value="{{ old('marks', 1) }}"
                                       min="1" 
                                       max="10" 
                                       required>
                                @error('marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Points awarded for correct answer</div>
                            </div>
                        </div>

                        <!-- Options Section -->
                        <div class="mb-4 mt-4">
                            <h5 class="text-primary"><i class="fas fa-list me-2"></i>Answer Options</h5>
                            <hr>
                        </div>

                        <div id="options-container">
                            <!-- Options will be dynamically added here -->
                        </div>

                        <button type="button" id="add-option" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Option
                        </button>

                        <!-- Correct Answer -->
                        <div class="mb-3 mt-4">
                            <label for="correct_answer" class="form-label">
                                <i class="fas fa-check-circle me-1"></i>Correct Answer <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('correct_answer') is-invalid @enderror" 
                                    id="correct_answer" 
                                    name="correct_answer" 
                                    required>
                                <option value="">Select Correct Answer</option>
                            </select>
                            @error('correct_answer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column - Preview -->
                    <div class="col-lg-4">
                        <div class="preview-section">
                            <h5 class="text-success"><i class="fas fa-eye me-2"></i>Preview</h5>
                            <hr>
                            
                            <div class="question-preview">
                                <div id="preview-test" class="text-muted small mb-2">No test selected</div>
                                <div id="preview-type" class="badge bg-secondary mb-3">Question Type</div>
                                <div id="preview-marks" class="badge bg-info mb-3 ms-2">0 marks</div>
                                
                                <div id="preview-question" class="fw-bold mb-3">
                                    Your question will appear here...
                                </div>
                                
                                <div id="preview-options">
                                    <p class="text-muted">Options will appear here after adding them...</p>
                                </div>
                                
                                <div id="preview-correct" class="mt-3 text-success" style="display: none;">
                                    <i class="fas fa-check-circle me-1"></i>
                                    <small>Correct: <span id="preview-correct-text"></span></small>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-light rounded">
                                <h6 class="text-info"><i class="fas fa-lightbulb me-1"></i>Tips</h6>
                                <ul class="small mb-0">
                                    <li>Write clear, unambiguous questions</li>
                                    <li>Ensure only one correct answer</li>
                                    <li>Use appropriate difficulty level</li>
                                    <li>Avoid negative phrasing when possible</li>
                                    <li>Review your question before saving</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <div>
                            <button type="submit" name="action" value="save" class="btn btn-primary me-2">
                                <i class="fas fa-save me-2"></i>Create Question
                            </button>
                            <button type="submit" name="action" value="save_and_add_another" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Create & Add Another
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let optionCount = 0;
        const oldOptions = @json(old('options', []));
        const oldCorrectAnswer = @json(old('correct_answer'));

        // Initialize form
        document.addEventListener('DOMContentLoaded', function() {
            updateQuestionType();
            
            // Load old values if they exist
            if (oldOptions.length > 0) {
                oldOptions.forEach(option => {
                    addOption(option);
                });
                updateCorrectAnswerOptions();
                document.getElementById('correct_answer').value = oldCorrectAnswer;
            }
        });

        // Question Type Change Handler
        document.getElementById('question_type').addEventListener('change', updateQuestionType);

        function updateQuestionType() {
            const questionType = document.getElementById('question_type').value;
            const container = document.getElementById('options-container');
            const addButton = document.getElementById('add-option');
            
            // Clear existing options
            container.innerHTML = '';
            optionCount = 0;
            updateCorrectAnswerOptions();
            
            if (questionType === 'mcq') {
                // Add default options for MCQ
                addOption('');
                addOption('');
                addButton.style.display = 'inline-block';
            } else if (questionType === 'true_false') {
                // Add True/False options
                addOption('True');
                addOption('False');
                addButton.style.display = 'none';
            } else {
                addButton.style.display = 'none';
            }
            
            updatePreview();
        }

        // Add Option Function
        document.getElementById('add-option').addEventListener('click', function() {
            addOption('');
        });

        function addOption(value = '') {
            if (optionCount >= 4) {
                alert('Maximum 4 options allowed');
                return;
            }
            
            optionCount++;
            const container = document.getElementById('options-container');
            const questionType = document.getElementById('question_type').value;
            const isReadonly = questionType === 'true_false' ? 'readonly' : '';
            
            const optionHtml = `
                <div class="option-group" data-option="${optionCount}">
                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            <span class="badge bg-primary">${String.fromCharCode(64 + optionCount)}</span>
                        </div>
                        <div class="flex-grow-1">
                            <input type="text" 
                                   class="form-control option-input" 
                                   name="options[]" 
                                   value="${value}"
                                   placeholder="Enter option ${optionCount}"
                                   ${isReadonly}
                                   required>
                        </div>
                        ${questionType !== 'true_false' ? `
                        <div class="ms-2">
                            <span class="remove-option" onclick="removeOption(${optionCount})">
                                <i class="fas fa-times"></i>
                            </span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', optionHtml);
            updateCorrectAnswerOptions();
            updatePreview();
            
            // Add event listeners
            const newInput = container.querySelector(`[data-option="${optionCount}"] .option-input`);
            newInput.addEventListener('input', updatePreview);
            newInput.addEventListener('input', updateCorrectAnswerOptions);
        }

        function removeOption(optionNumber) {
            const questionType = document.getElementById('question_type').value;
            if (questionType === 'true_false') return;
            
            const optionGroups = document.querySelectorAll('.option-group');
            if (optionGroups.length <= 2) {
                alert('Minimum 2 options required');
                return;
            }
            
            document.querySelector(`[data-option="${optionNumber}"]`).remove();
            updateCorrectAnswerOptions();
            updatePreview();
        }

        function updateCorrectAnswerOptions() {
            const correctSelect = document.getElementById('correct_answer');
            const options = document.querySelectorAll('.option-input');
            
            // Save current selection
            const currentValue = correctSelect.value;
            
            // Clear options
            correctSelect.innerHTML = '<option value="">Select Correct Answer</option>';
            
            // Add options
            options.forEach((input, index) => {
                if (input.value.trim()) {
                    const option = document.createElement('option');
                    option.value = input.value;
                    option.textContent = `${String.fromCharCode(65 + index)}. ${input.value}`;
                    if (input.value === currentValue) {
                        option.selected = true;
                    }
                    correctSelect.appendChild(option);
                }
            });
        }

        // Live Preview Updates
        function updatePreview() {
            // Test selection
            const testSelect = document.getElementById('entry_test_id');
            const selectedTest = testSelect.options[testSelect.selectedIndex];
            document.getElementById('preview-test').textContent = 
                selectedTest && selectedTest.value ? selectedTest.text : 'No test selected';
            
            // Question type and marks
            const questionType = document.getElementById('question_type').value;
            const marks = document.getElementById('marks').value;
            
            document.getElementById('preview-type').textContent = 
                questionType === 'mcq' ? 'Multiple Choice' : 
                questionType === 'true_false' ? 'True/False' : 'Question Type';
            
            document.getElementById('preview-marks').textContent = (marks || 0) + ' marks';
            
            // Question text
            const questionText = document.getElementById('question_text').value;
            document.getElementById('preview-question').textContent = 
                questionText || 'Your question will appear here...';
            
            // Options
            const options = document.querySelectorAll('.option-input');
            const optionsContainer = document.getElementById('preview-options');
            
            if (options.length > 0) {
                let optionsHtml = '';
                options.forEach((input, index) => {
                    if (input.value.trim()) {
                        optionsHtml += `
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" disabled>
                                <label class="form-check-label">
                                    ${String.fromCharCode(65 + index)}. ${input.value}
                                </label>
                            </div>
                        `;
                    }
                });
                optionsContainer.innerHTML = optionsHtml || '<p class="text-muted">Options will appear here...</p>';
            } else {
                optionsContainer.innerHTML = '<p class="text-muted">Options will appear here...</p>';
            }
            
            // Correct answer
            const correctAnswer = document.getElementById('correct_answer').value;
            const correctDiv = document.getElementById('preview-correct');
            const correctText = document.getElementById('preview-correct-text');
            
            if (correctAnswer) {
                correctText.textContent = correctAnswer;
                correctDiv.style.display = 'block';
            } else {
                correctDiv.style.display = 'none';
            }
        }

        // Event listeners for live preview
        document.getElementById('entry_test_id').addEventListener('change', updatePreview);
        document.getElementById('question_text').addEventListener('input', updatePreview);
        document.getElementById('marks').addEventListener('input', updatePreview);
        document.getElementById('correct_answer').addEventListener('change', updatePreview);

        // Form submission validation
        document.getElementById('createForm').addEventListener('submit', function(e) {
            const options = document.querySelectorAll('.option-input');
            const correctAnswer = document.getElementById('correct_answer').value;
            
            // Check if all options are filled
            let emptyOptions = 0;
            options.forEach(input => {
                if (!input.value.trim()) emptyOptions++;
            });
            
            if (emptyOptions > 0) {
                e.preventDefault();
                alert('Please fill in all option fields.');
                return;
            }
            
            // Check if correct answer is selected
            if (!correctAnswer) {
                e.preventDefault();
                alert('Please select the correct answer.');
                return;
            }
            
            // Check if correct answer exists in options
            let answerExists = false;
            options.forEach(input => {
                if (input.value === correctAnswer) answerExists = true;
            });
            
            if (!answerExists) {
                e.preventDefault();
                alert('The selected correct answer must match one of the options.');
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