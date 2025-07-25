{{-- File: resources/views/admin/entry-tests/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $entryTest->title }} - Admin LMS</title>
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
        .status-badge {
            font-size: 1rem;
            padding: 8px 15px;
            border-radius: 20px;
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
                <h2><i class="fas fa-clipboard-list me-2"></i>{{ $entryTest->title }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.entry-tests.index') }}">Entry Tests</a></li>
                        <li class="breadcrumb-item active">{{ $entryTest->title }}</li>
                    </ol>
                </nav>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.entry-tests.edit', $entryTest) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit Test
                </a>
                <a href="{{ route('admin.questions.index') }}?entry_test={{ $entryTest->id }}" class="btn btn-info">
                    <i class="fas fa-question-circle me-2"></i>Manage Questions
                </a>
                <a href="{{ route('admin.entry-tests.index') }}" class="btn btn-secondary">
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
                <!-- Test Information -->
                <div class="info-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h4 class="text-primary"><i class="fas fa-info-circle me-2"></i>Test Information</h4>
                        @if($entryTest->is_active)
                            <span class="badge bg-success status-badge">
                                <i class="fas fa-check-circle me-1"></i>Active
                            </span>
                        @else
                            <span class="badge bg-danger status-badge">
                                <i class="fas fa-pause-circle me-1"></i>Inactive
                            </span>
                        @endif
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Description</h6>
                            <p class="mb-3">{{ $entryTest->description }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6">
                                    <h6 class="text-muted">Duration</h6>
                                    <p class="fw-bold text-info">
                                        <i class="fas fa-clock me-1"></i>{{ $entryTest->duration_minutes }} minutes
                                    </p>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-muted">Passing Score</h6>
                                    <p class="fw-bold text-warning">
                                        <i class="fas fa-percentage me-1"></i>{{ $entryTest->passing_score }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Created</h6>
                            <p><i class="fas fa-calendar me-1"></i>{{ $entryTest->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Last Updated</h6>
                            <p><i class="fas fa-sync me-1"></i>{{ $entryTest->updated_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="text-muted mb-3">Quick Actions</h6>
                        <div class="action-buttons">
                            <form method="POST" action="{{ route('admin.entry-tests.toggle-status', $entryTest) }}" class="d-inline">
                                @csrf
                                <button type="submit" 
                                        class="btn btn-sm {{ $entryTest->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                        onclick="return confirm('Are you sure you want to {{ $entryTest->is_active ? 'deactivate' : 'activate' }} this test?')">
                                    <i class="fas fa-{{ $entryTest->is_active ? 'pause' : 'play' }} me-1"></i>
                                    {{ $entryTest->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            
                            @if($entryTest->attempts_count == 0)
                                <form method="POST" action="{{ route('admin.entry-tests.destroy', $entryTest) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure you want to delete this entry test? This action cannot be undone.')">
                                        <i class="fas fa-trash me-1"></i>Delete Test
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recent Attempts -->
                @if($entryTest->attempts->count() > 0)
                <div class="table-card">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="text-primary mb-0">
                            <i class="fas fa-list-alt me-2"></i>Recent Attempts
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>Result</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entryTest->attempts->take(10) as $attempt)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $attempt->student->name ?? 'Unknown' }}</div>
                                                    <small class="text-muted">{{ $attempt->student->email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{ $attempt->created_at->format('M j, Y') }}</div>
                                            <small class="text-muted">{{ $attempt->created_at->format('g:i A') }}</small>
                                        </td>
                                        <td>
                                            @if($attempt->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($attempt->status === 'in_progress')
                                                <span class="badge bg-warning">In Progress</span>
                                            @elseif($attempt->status === 'expired')
                                                <span class="badge bg-secondary">Expired</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ ucfirst($attempt->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attempt->status === 'completed')
                                                <div class="fw-bold">{{ number_format($attempt->percentage, 1) }}%</div>
                                                <small class="text-muted">{{ $attempt->obtained_marks }}/{{ $attempt->total_marks }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attempt->status === 'completed')
                                                @if($attempt->percentage >= $entryTest->passing_score)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Passed
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Failed
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.student-attempts.show', $attempt) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($entryTest->attempts->count() > 10)
                        <div class="card-footer bg-white border-0 text-center">
                            <a href="{{ route('admin.student-attempts.index') }}?entry_test={{ $entryTest->id }}" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>View All Attempts ({{ $entryTest->attempts_count }})
                            </a>
                        </div>
                    @endif
                </div>
                @else
                <div class="info-card text-center">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Attempts Yet</h5>
                    <p class="text-muted">Students haven't taken this test yet. Make sure the test is active and has questions.</p>
                    @if($entryTest->questions_count == 0)
                        <a href="{{ route('admin.questions.create') }}?entry_test={{ $entryTest->id }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Questions
                        </a>
                    @endif
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Statistics -->
                <div class="stats-card">
                    <h5 class="mb-4"><i class="fas fa-chart-line me-2"></i>Test Statistics</h5>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="metric-card">
                                <div class="h4 text-primary mb-1">{{ $stats['total_questions'] ?? 0 }}</div>
                                <div class="text-muted small">Questions</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="metric-card">
                                <div class="h4 text-info mb-1">{{ $stats['total_attempts'] ?? 0 }}</div>
                                <div class="text-muted small">Total Attempts</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="metric-card">
                                <div class="h4 text-success mb-1">{{ $stats['completed_attempts'] ?? 0 }}</div>
                                <div class="text-muted small">Completed</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="metric-card">
                                <div class="h4 text-warning mb-1">{{ $stats['passed_attempts'] ?? 0 }}</div>
                                <div class="text-muted small">Passed</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($stats['average_score'] !== null)
                        <div class="mt-3 pt-3 border-top border-light">
                            <div class="text-center">
                                <div class="h5 mb-1">{{ number_format($stats['average_score'], 1) }}%</div>
                                <div class="text-light small">Average Score</div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Test Configuration -->
                <div class="info-card">
                    <h5 class="text-primary mb-3"><i class="fas fa-cogs me-2"></i>Configuration</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Questions:</span>
                            <span class="fw-bold">{{ $entryTest->total_questions }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Current Questions:</span>
                            <span class="fw-bold {{ $stats['total_questions'] < $entryTest->total_questions ? 'text-warning' : 'text-success' }}">
                                {{ $stats['total_questions'] }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Duration:</span>
                            <span class="fw-bold">{{ $entryTest->duration_minutes }} min</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Passing Score:</span>
                            <span class="fw-bold">{{ $entryTest->passing_score }}%</span>
                        </div>
                    </div>

                    @if($stats['total_questions'] < $entryTest->total_questions)
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Incomplete:</strong> Add {{ $entryTest->total_questions - $stats['total_questions'] }} more question(s).
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="info-card">
                    <h5 class="text-primary mb-3"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.questions.create') }}?entry_test={{ $entryTest->id }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Add Question
                        </a>
                        
                        <a href="{{ route('admin.questions.index') }}?entry_test={{ $entryTest->id }}" class="btn btn-outline-info">
                            <i class="fas fa-list me-2"></i>View Questions
                        </a>
                        
                        @if($entryTest->attempts_count > 0)
                            <a href="{{ route('admin.student-attempts.index') }}?entry_test={{ $entryTest->id }}" class="btn btn-outline-success">
                                <i class="fas fa-chart-bar me-2"></i>View Attempts
                            </a>
                        @endif
                        
                        <a href="{{ route('admin.entry-tests.edit', $entryTest) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit me-2"></i>Edit Settings
                        </a>
                    </div>
                </div>

                <!-- System Status -->
                <div class="info-card">
                    <h5 class="text-primary mb-3"><i class="fas fa-heartbeat me-2"></i>Status Check</h5>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Test Active:</span>
                            <span class="badge {{ $entryTest->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $entryTest->is_active ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Has Questions:</span>
                            <span class="badge {{ $stats['total_questions'] > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $stats['total_questions'] > 0 ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Ready for Students:</span>
                            <span class="badge {{ ($entryTest->is_active && $stats['total_questions'] > 0) ? 'bg-success' : 'bg-warning' }}">
                                {{ ($entryTest->is_active && $stats['total_questions'] > 0) ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>

                    @if(!$entryTest->is_active || $stats['total_questions'] == 0)
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>
                                @if(!$entryTest->is_active && $stats['total_questions'] == 0)
                                    Activate test and add questions to make it available to students.
                                @elseif(!$entryTest->is_active)
                                    Activate the test to make it available to students.
                                @else
                                    Add questions to make the test available to students.
                                @endif
                            </small>
                        </div>
                    @endif
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