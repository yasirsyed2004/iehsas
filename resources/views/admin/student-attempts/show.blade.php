{{-- File: resources/views/admin/student-attempts/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attempt Details - Admin LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    {{-- Include Shared Admin Styles --}}
    @include('admin.partials.styles')

    {{-- Page-specific styles --}}
    <style>
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: translate(20px, -20px);
        }
        .metric-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .metric-card:hover {
            transform: translateY(-5px);
        }
        .student-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
            margin: 0 auto 15px;
        }
        .question-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #dee2e6;
        }
        .question-card.correct {
            border-left-color: #28a745;
            background-color: #f8fff9;
        }
        .question-card.incorrect {
            border-left-color: #dc3545;
            background-color: #fff8f8;
        }
        .option-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 15px;
            margin: 5px 0;
        }
        .option-item.selected {
            background-color: #e3f2fd;
            border-color: #2196f3;
        }
        .option-item.correct {
            background-color: #e8f5e8;
            border-color: #4caf50;
        }
        .option-item.incorrect {
            background-color: #ffebee;
            border-color: #f44336;
        }
        .btn {
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .progress-circle {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto;
        }
        .progress-circle svg {
            transform: rotate(-90deg);
        }
        .progress-circle-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.5rem;
            font-weight: bold;
        }
        .hover-shadow {
            transition: all 0.3s ease;
        }
        .hover-shadow:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
        <!-- Sidebar -->
    {{-- Include Shared Sidebar --}}
    @include('admin.partials.sidebar', ['activeMenu' => 'student-attempts'])

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-user-graduate me-2"></i>{{ $attempt->student->full_name ?? 'Unknown Student' }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.student-attempts.index') }}">Student Attempts</a></li>
                        <li class="breadcrumb-item active">Attempt #{{ $attempt->id }}</li>
                    </ol>
                </nav>
            </div>
            <div class="action-buttons">
                @if($attempt->status === 'completed' && !($attempt->student->is_retake_allowed ?? false))
                    <form method="POST" action="{{ route('admin.student-attempts.allow-retake', $attempt) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Allow retake for this student?')">
                            <i class="fas fa-redo me-2"></i>Allow Retake
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.entry-tests.show', $attempt->entryTest) }}" class="btn btn-info">
                    <i class="fas fa-clipboard-list me-2"></i>View Test
                </a>
                <a href="{{ route('admin.student-attempts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
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

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Student Information -->
                <div class="info-card">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="student-avatar">
                                {{ substr($attempt->student->full_name ?? 'U', 0, 1) }}
                            </div>
                            @if($attempt->status === 'completed')
                                @php
                                    $passed = $attempt->percentage >= ($attempt->entryTest->passing_score ?? 0);
                                @endphp
                                <span class="badge {{ $passed ? 'bg-success' : 'bg-danger' }} fs-6">
                                    {{ $passed ? 'PASSED' : 'FAILED' }}
                                </span>
                            @else
                                <span class="badge bg-warning fs-6">{{ strtoupper($attempt->status) }}</span>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h4 class="text-primary mb-3">Student Information</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Full Name</h6>
                                    <p class="fw-bold">{{ $attempt->student->full_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Email</h6>
                                    <p class="fw-bold">{{ $attempt->student->email ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">{{ $attempt->student->id_type_label ?? 'ID' }}</h6>
                                    <p class="fw-bold">{{ $attempt->student->formatted_id ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Contact</h6>
                                    <p class="fw-bold">{{ $attempt->student->contact_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($attempt->student->selectedCourses && $attempt->student->selectedCourses->count() > 0)
<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
        <h5 class="mb-0 text-white">
            <i class="fas fa-graduation-cap me-2"></i>Selected Courses
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($attempt->student->selectedCourses as $course)
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 border rounded hover-shadow">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-book text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $course->title }}</h6>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-{{ $course->level === 'beginner' ? 'success' : ($course->level === 'intermediate' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($course->level) }}
                                </span>
                                @if($course->pivot && $course->pivot->selected_at)
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($course->pivot->selected_at)->format('M d, Y') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-3 pt-3 border-top">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Total {{ $attempt->student->selectedCourses->count() }} course(s) selected during registration
            </small>
        </div>
    </div>
</div>
@endif

                <!-- Test Information -->
                <div class="info-card">
                    <h4 class="text-primary mb-3"><i class="fas fa-clipboard-list me-2"></i>Test Information</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Test Title</h6>
                            <p class="fw-bold">{{ $attempt->entryTest->title ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Test Duration</h6>
                            <p class="fw-bold">{{ $attempt->entryTest->duration_minutes ?? 0 }} minutes</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Passing Score</h6>
                            <p class="fw-bold">{{ $attempt->entryTest->passing_score ?? 0 }}%</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Total Questions</h6>
                            <p class="fw-bold">{{ $attempt->entryTest->total_questions ?? 0 }}</p>
                        </div>
                        <div class="col-md-12">
                            <h6 class="text-muted">Description</h6>
                            <p>{{ $attempt->entryTest->description ?? 'No description available' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Attempt Timeline -->
                <div class="info-card">
                    <h4 class="text-primary mb-3"><i class="fas fa-clock me-2"></i>Attempt Timeline</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Started At</h6>
                            <p class="fw-bold">
                                @if($attempt->started_at)
                                    <i class="fas fa-calendar me-1"></i>{{ $attempt->started_at->format('M j, Y \a\t g:i A') }}
                                @else
                                    <span class="text-muted">Not started</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Completed At</h6>
                            <p class="fw-bold">
                                @if($attempt->completed_at)
                                    <i class="fas fa-calendar me-1"></i>{{ $attempt->completed_at->format('M j, Y \a\t g:i A') }}
                                @else
                                    <span class="text-muted">Not completed</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Time Taken</h6>
                            <p class="fw-bold">
                                @if($attempt->completed_at && $attempt->started_at)
                                    @php
                                        $duration = $attempt->started_at->diffInMinutes($attempt->completed_at);
                                    @endphp
                                    <i class="fas fa-clock me-1"></i>{{ $duration }} minutes
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Expires At</h6>
                            <p class="fw-bold">
                                @if($attempt->expires_at)
                                    <i class="fas fa-calendar me-1"></i>{{ $attempt->expires_at->format('M j, Y \a\t g:i A') }}
                                @else
                                    <span class="text-muted">No expiration</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Questions and Answers -->
                @if($attempt->status === 'completed' && $attempt->answers->count() > 0)
                <div class="info-card">
                    <h4 class="text-primary mb-3"><i class="fas fa-list-alt me-2"></i>Questions & Answers</h4>
                    
                    @foreach($attempt->answers as $index => $answer)
                        <div class="question-card {{ $answer->is_correct ? 'correct' : 'incorrect' }}">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="fw-bold">Question {{ $index + 1 }}</h6>
                                <div class="d-flex align-items-center">
                                    @if($answer->is_correct)
                                        <span class="badge bg-success me-2">
                                            <i class="fas fa-check"></i> Correct
                                        </span>
                                    @else
                                        <span class="badge bg-danger me-2">
                                            <i class="fas fa-times"></i> Incorrect
                                        </span>
                                    @endif
                                    <span class="badge bg-primary">{{ $answer->marks_obtained }}/{{ $answer->question->marks ?? 0 }} marks</span>
                                </div>
                            </div>
                            
                            <div class="question-text mb-3">
                                <strong>{{ $answer->question->question_text ?? 'Question not found' }}</strong>
                            </div>
                            
                            @if(is_array($answer->question->options ?? []))
                                <div class="options-section">
                                    @foreach($answer->question->options as $optionIndex => $option)
                                        <div class="option-item 
                                            {{ $option === $answer->selected_answer ? 'selected' : '' }}
                                            {{ $option === $answer->question->correct_answer ? 'correct' : ($option === $answer->selected_answer ? 'incorrect' : '') }}">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-secondary me-2">{{ chr(65 + $optionIndex) }}</span>
                                                <span class="flex-grow-1">{{ $option }}</span>
                                                @if($option === $answer->question->correct_answer)
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @endif
                                                @if($option === $answer->selected_answer && $option !== $answer->question->correct_answer)
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <strong>Selected:</strong> {{ $answer->selected_answer ?? 'No answer' }} | 
                                    <strong>Correct:</strong> {{ $answer->question->correct_answer ?? 'N/A' }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Score Card -->
                <div class="stats-card">
                    <h5 class="mb-4"><i class="fas fa-chart-pie me-2"></i>Test Results</h5>
                    
                    @if($attempt->status === 'completed')
                        <div class="progress-circle mb-3">
                            <svg width="120" height="120">
                                <circle cx="60" cy="60" r="50" 
                                        stroke="rgba(255,255,255,0.3)" 
                                        stroke-width="8" 
                                        fill="none"/>
                                <circle cx="60" cy="60" r="50" 
                                        stroke="white" 
                                        stroke-width="8" 
                                        fill="none"
                                        stroke-dasharray="{{ 2 * 3.14159 * 50 }}"
                                        stroke-dashoffset="{{ 2 * 3.14159 * 50 * (1 - ($attempt->percentage ?? 0) / 100) }}"/>
                            </svg>
                            <div class="progress-circle-text text-white">
                                {{ number_format($attempt->percentage ?? 0, 1) }}%
                            </div>
                        </div>
                        
                        <div class="text-center text-white">
                            <h4>{{ $attempt->obtained_marks ?? 0 }}/{{ $attempt->total_marks ?? 0 }}</h4>
                            <p class="mb-0">Total Score</p>
                        </div>
                    @else
                        <div class="text-center">
                            <i class="fas fa-hourglass-half fa-3x mb-3 text-white opacity-50"></i>
                            <h5 class="text-white">Test {{ ucfirst($attempt->status) }}</h5>
                            <p class="text-white opacity-75">Results will be available after completion</p>
                        </div>
                    @endif
                </div>

                <!-- Performance Metrics -->
                @if($attempt->status === 'completed')
                <div class="info-card">
                    <h5 class="text-primary mb-3"><i class="fas fa-chart-bar me-2"></i>Performance Metrics</h5>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="metric-card">
                                <div class="h4 text-success mb-1">{{ $attempt->answers->where('is_correct', true)->count() }}</div>
                                <div class="text-muted small">Correct</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="metric-card">
                                <div class="h4 text-danger mb-1">{{ $attempt->answers->where('is_correct', false)->count() }}</div>
                                <div class="text-muted small">Incorrect</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="metric-card">
                                <div class="h4 text-info mb-1">{{ $attempt->answers->count() }}</div>
                                <div class="text-muted small">Attempted</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="metric-card">
                                <div class="h4 text-warning mb-1">{{ ($attempt->entryTest->total_questions ?? 0) - $attempt->answers->count() }}</div>
                                <div class="text-muted small">Skipped</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Accuracy:</span>
                            <span class="fw-bold">
                                {{ $attempt->answers->count() > 0 ? number_format(($attempt->answers->where('is_correct', true)->count() / $attempt->answers->count()) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Completion:</span>
                            <span class="fw-bold">
                                {{ ($attempt->entryTest->total_questions ?? 0) > 0 ? number_format(($attempt->answers->count() / ($attempt->entryTest->total_questions ?? 1)) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="info-card">
                    <h5 class="text-primary mb-3"><i class="fas fa-cogs me-2"></i>Actions</h5>
                    
                    <div class="d-grid gap-2">
                        @if($attempt->status === 'completed' && !($attempt->student->is_retake_allowed ?? false))
                            <form method="POST" action="{{ route('admin.student-attempts.allow-retake', $attempt) }}">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Allow retake for this student?')">
                                    <i class="fas fa-redo me-2"></i>Allow Retake
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('admin.entry-tests.show', $attempt->entryTest) }}" class="btn btn-info w-100">
                            <i class="fas fa-clipboard-list me-2"></i>View Test Details
                        </a>
                        
                        <a href="{{ route('admin.student-attempts.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-list me-2"></i>All Attempts
                        </a>
                        
                        <form method="POST" action="{{ route('admin.student-attempts.destroy', $attempt) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete this attempt?')">
                                <i class="fas fa-trash me-2"></i>Delete Attempt
                            </button>
                        </form>
                    </div>
                </div>

                <!-- System Information -->
                <div class="info-card">
                    <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>System Info</h5>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Attempt ID:</span>
                            <span class="fw-bold">#{{ $attempt->id }}</span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Status:</span>
                            <span class="badge {{ $attempt->status === 'disqualified' ? 'bg-danger' : 'bg-primary' }}">{{ ucfirst($attempt->status) }}</span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Tab Switches:</span>
                            <span class="badge {{ ($attempt->browser_switches ?? 0) > 0 ? 'bg-danger' : 'bg-success' }}">{{ $attempt->browser_switches ?? 0 }}</span>
                        </div>
                    </div>

                    @if($attempt->proctoring_violations)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Violations:</span>
                                <span class="badge bg-warning">{{ count($attempt->proctoring_violations) }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Created:</span>
                            <span class="fw-bold">{{ $attempt->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Proctoring Violations Detail -->
                @if($attempt->proctoring_violations && count($attempt->proctoring_violations) > 0)
                <div class="info-card">
                    <h5 class="text-danger mb-3"><i class="fas fa-shield-alt me-2"></i>Proctoring Violations</h5>
                    <div style="max-height: 300px; overflow-y: auto;">
                        @foreach($attempt->proctoring_violations as $violation)
                            <div class="d-flex align-items-start mb-2 p-2 border rounded">
                                <span class="badge bg-{{ in_array($violation['type'] ?? '', ['tab_switch', 'tab_switch_disqualified', 'multiple_faces', 'face_not_detected']) ? 'danger' : 'warning' }} me-2" style="font-size: 0.7rem; white-space: nowrap;">
                                    {{ str_replace('_', ' ', ucfirst($violation['type'] ?? 'unknown')) }}
                                </span>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">{{ $violation['details'] ?? '' }}</small>
                                    <small class="text-muted">{{ isset($violation['timestamp']) ? \Carbon\Carbon::parse($violation['timestamp'])->format('g:i:s A') : '' }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Exam Screenshots -->
                @if($attempt->screenshots && $attempt->screenshots->count() > 0)
                <div class="info-card">
                    <h5 class="text-primary mb-3"><i class="fas fa-camera me-2"></i>Exam Screenshots ({{ $attempt->screenshots->count() }})</h5>
                    <div class="row g-2" style="max-height: 400px; overflow-y: auto;">
                        @foreach($attempt->screenshots as $screenshot)
                            <div class="col-6">
                                <div class="border rounded overflow-hidden cursor-pointer" data-bs-toggle="modal" data-bs-target="#screenshotModal{{ $screenshot->id }}">
                                    <img src="{{ route('admin.exam-screenshot', $screenshot->id) }}"
                                         alt="Screenshot"
                                         class="w-100"
                                         style="height: 80px; object-fit: cover;">
                                    <div class="p-1 text-center bg-light">
                                        <small class="text-muted">{{ $screenshot->captured_at->format('g:i:s A') }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Screenshot Modal -->
                            <div class="modal fade" id="screenshotModal{{ $screenshot->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h6 class="modal-title">Screenshot - {{ $screenshot->captured_at->format('M j, Y g:i:s A') }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <img src="{{ route('admin.exam-screenshot', $screenshot->id) }}" alt="Screenshot" class="w-100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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