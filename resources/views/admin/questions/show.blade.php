{{-- File: resources/views/admin/questions/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question #{{ $question->id }} - Admin LMS</title>
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
        .question-preview {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border-left: 5px solid #007bff;
        }
        .option-item {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        .option-item.correct {
            border-color: #28a745;
            background-color: #f8fff9;
        }
        .option-item.incorrect {
            border-color: #dc3545;
            background-color: #fff8f8;
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
        .table-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .table thead {
            background: #f8f9fa;
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
                <h2><i class="fas fa-question-circle me-2"></i>Question #{{ $question->id }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">Question Bank</a></li>
                        <li class="breadcrumb-item active">Question #{{ $question->id }}</li>
                    </ol>
                </nav>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.questions.edit', $question) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit Question
                </a>
                <a href="{{ route('admin.entry-tests.show', $question->entryTest) }}" class="btn btn-info">
                    <i class="fas fa-clipboard-list me-2"></i>View Test
                </a>
                <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">
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
                <!-- Question Preview -->
                <div class="question-preview">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="text-primary mb-2">
                                <i class="fas fa-question-circle me-2"></i>Question Preview
                            </h4>
                            <div class="mb-2">
                                <span class="badge bg-primary me-2">
                                    {{ $question->question_type === 'mcq' ? 'Multiple Choice' : 'True/False' }}
                                </span>
                                <span class="badge bg-success me-2">{{ $question->marks }} marks</span>
                                <span class="badge bg-info">Order: {{ $question->order }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="question-text mb-4">
                        <h5 class="fw-bold">{{ $question->question_text }}</h5>
                    </div>

                    <div class="options-section">
                        <h6 class="text-muted mb-3">Answer Options:</h6>
                        @if(is_array($question->options))
                            @foreach($question->options as $index => $option)
                                <div class="option-item {{ $option === $question->correct_answer ? 'correct' : '' }}">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <span class="badge {{ $option === $question->correct_answer ? 'bg-success' : 'bg-secondary' }}">
                                                {{ chr(65 + $index) }}
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            {{ $option }}
                                        </div>
                                        @if($option === $question->correct_answer)
                                            <div class="ms-2">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <small class="text-success fw-bold">Correct</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Entry Test:</h6>
                                <p class="fw-bold">
                                    <i class="fas fa-clipboard-list me-1"></i>
                                    {{ $question->entryTest->title ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Correct Answer:</h6>
                                <p class="fw-bold text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $question->correct_answer }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Question Details -->
                <div class="info-card">
                    <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Question Details</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Question ID</h6>
                            <p class="fw-bold">#{{ $question->id }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Question Type</h6>
                            <p class="fw-bold">{{ $question->question_type === 'mcq' ? 'Multiple Choice Question' : 'True/False Question' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Marks</h6>
                            <p class="fw-bold">{{ $question->marks }} point(s)</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Order in Test</h6>
                            <p class="fw-bold">{{ $question->order }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Created</h6>
                            <p><i class="fas fa-calendar me-1"></i>{{ $question->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Last Updated</h6>
                            <p><i class="fas fa-sync me-1"></i>{{ $question->updated_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="text-muted mb-3">Quick Actions</h6>
                        <div class="action-buttons">
                            <a href="{{ route('admin.questions.edit', $question) }}" class="btn btn-outline-warning">
                                <i class="fas fa-edit me-1"></i>Edit Question
                            </a>
                            
                            @if(!$question->answers()->exists())
                                <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-outline-danger"
                                            onclick="return confirm('Are you sure you want to delete this question? This action cannot be undone.')">
                                        <i class="fas fa-trash me-1"></i>Delete Question
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-outline-danger" disabled title="Cannot delete - has answers">
                                    <i class="fas fa-trash me-1"></i>Cannot Delete
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Answer History -->
                @if($question->answers()->exists())
                <div class="table-card">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="text-primary mb-0">
                            <i class="fas fa-history me-2"></i>Recent Answers
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Selected Answer</th>
                                    <th>Result</th>
                                    <th>Marks</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($question->answers()->with('attempt.student')->latest()->take(10)->get() as $answer)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $answer->attempt->student->name ?? 'Unknown' }}</div>
                                                    <small class="text-muted">{{ $answer->attempt->student->email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $answer->selected_answer }}</span>
                                        </td>
                                        <td>
                                            @if($answer->is_correct)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Correct
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>Incorrect
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $answer->marks_obtained }}/{{ $question->marks }}</span>
                                        </td>
                                        <td>
                                            <div>{{ $answer->created_at->format('M j, Y') }}</div>
                                            <small class="text-muted">{{ $answer->created_at->format('g:i A') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($question->answers()->count() > 10)
                        <div class="card-footer bg-white border-0 text-center">
                            <small class="text-muted">Showing latest 10 of {{ $question->answers()->count() }} total answers</small>
                        </div>
                    @endif
                </div>
                @else
                <div class="info-card text-center">
                    <i class="fas fa-clipboard-question fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Answers Yet</h5>
                    <p class="text-muted">This question hasn't been answered by any students yet.</p>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Statistics -->
                <div class="stats-card">
                    <h5 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Question Statistics</h5>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="metric-card">
                                <div class="h4 text-primary mb-1">{{ $question->answers()->count() }}</div>
                                <div class="text-muted small">Total Attempts</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="metric-card">
                                <div class="h4 text-success mb-1">{{ $question->answers()->where('is_correct', true)->count() }}</div>
                                <div class="text-muted small">Correct</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="metric-card">
                                <div class="h4 text-danger mb-1">{{ $question->answers()->where('is_correct', false)->count() }}</div>
                                <div class="text-muted small">Incorrect</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="metric-card">
                                <div class="h4 text-info mb-1">{{ $question->marks }}</div>
                                <div class="text-muted small">Max Marks</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($question->answers()->count() > 0)
                        <div class="mt-3 pt-3 border-top border-light">
                            <div class="text-center">
                                <div class="h5 mb-1">
                                    {{ number_format(($question->answers()->where('is_correct', true)->count() / $question->answers()->count()) * 100, 1) }}%
                                </div>
                                <div class="text-light small">Success Rate</div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Test Information -->
                <div class="info-card">
                    <h5 class="text-primary mb-3"><i class="fas fa-clipboard-list me-2"></i>Test Information</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Test Title:</span>
                            <span class="fw-bold">{{ $question->entryTest->title ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Test Status:</span>
                            <span class="badge {{ $question->entryTest->is_active ?? false ? 'bg-success' : 'bg-danger' }}">
                                {{ $question->entryTest->is_active ?? false ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Questions:</span>
                            <span class="fw-bold">{{ $question->entryTest->questions()->count() ?? 0 }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Test Duration:</span>
                            <span class="fw-bold">{{ $question->entryTest->duration_minutes ?? 0 }} min</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('admin.entry-tests.show', $question->entryTest) }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-external-link-alt me-2"></i>View Test Details
                        </a>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="info-card">
                    <h5 class="text-primary mb-3"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.questions.create') }}?entry_test={{ $question->entry_test_id }}" class="btn btn-outline-success">
                            <i class="fas fa-plus me-2"></i>Add Another Question
                        </a>
                        
                        <a href="{{ route('admin.questions.index') }}?entry_test={{ $question->entry_test_id }}" class="btn btn-outline-info">
                            <i class="fas fa-list me-2"></i>View All Questions
                        </a>
                        
                        <a href="{{ route('admin.questions.edit', $question) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit me-2"></i>Edit This Question
                        </a>
                    </div>
                </div>
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