{{-- File: resources/views/admin/student-attempts/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attempts - Admin LMS</title>
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
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            text-align: center;
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .table-responsive {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .filter-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .student-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .progress-bar-custom {
            height: 8px;
            border-radius: 4px;
        }
        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
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
                <a class="nav-link active" href="{{ route('admin.student-attempts.index') }}">
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
                <h2><i class="fas fa-chart-line me-2"></i>Student Attempts Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Student Attempts</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button class="btn btn-success" onclick="exportAttempts()">
                    <i class="fas fa-download me-2"></i>Export Data
                </button>
                <a href="{{ route('admin.entry-tests.index') }}" class="btn btn-info">
                    <i class="fas fa-clipboard-list me-2"></i>View Tests
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="h3 text-primary mb-1">{{ $stats['total_attempts'] ?? 0 }}</div>
                    <div class="text-muted">Total Attempts</div>
                    <div class="progress mt-2 progress-bar-custom">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="h3 text-success mb-1">{{ $stats['completed_attempts'] ?? 0 }}</div>
                    <div class="text-muted">Completed</div>
                    <div class="progress mt-2 progress-bar-custom">
                        <div class="progress-bar bg-success" 
                             style="width: {{ $stats['total_attempts'] > 0 ? round(($stats['completed_attempts'] / $stats['total_attempts']) * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="h3 text-warning mb-1">{{ $stats['passed_attempts'] ?? 0 }}</div>
                    <div class="text-muted">Passed</div>
                    <div class="progress mt-2 progress-bar-custom">
                        <div class="progress-bar bg-warning" 
                             style="width: {{ $stats['completed_attempts'] > 0 ? round(($stats['passed_attempts'] / $stats['completed_attempts']) * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="h3 text-info mb-1">{{ number_format($stats['average_score'] ?? 0, 1) }}%</div>
                    <div class="text-muted">Average Score</div>
                    <div class="progress mt-2 progress-bar-custom">
                        <div class="progress-bar bg-info" 
                             style="width: {{ $stats['average_score'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.student-attempts.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Result</label>
                    <select class="form-select" name="result">
                        <option value="">All Results</option>
                        <option value="passed" {{ request('result') == 'passed' ? 'selected' : '' }}>Passed</option>
                        <option value="failed" {{ request('result') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search Student</label>
                    <input type="text" class="form-control" name="search" 
                           value="{{ request('search') }}" placeholder="Name, CNIC, or Email...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Attempts Table -->
        <div class="table-responsive">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Student Attempts</h5>
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-sort me-1"></i>Sort By
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?sort=created_at&order=desc">Latest First</a></li>
                            <li><a class="dropdown-item" href="?sort=created_at&order=asc">Oldest First</a></li>
                            <li><a class="dropdown-item" href="?sort=percentage&order=desc">Highest Score</a></li>
                            <li><a class="dropdown-item" href="?sort=percentage&order=asc">Lowest Score</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            @if($attempts->count() > 0)
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Student</th>
                            <th>Entry Test</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Result</th>
                            <th>Duration</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attempts as $attempt)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="student-avatar me-3">
                                            {{ substr($attempt->student->full_name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $attempt->student->full_name ?? 'Unknown Student' }}</div>
                                            <small class="text-muted">{{ $attempt->student->cnic ?? 'N/A' }}</small>
                                            <br><small class="text-muted">{{ $attempt->student->email ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $attempt->entryTest->title ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $attempt->entryTest->duration_minutes ?? 0 }} minutes</small>
                                </td>
                                <td>
                                    @if($attempt->status === 'completed')
                                        <span class="status-indicator bg-success"></span>
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($attempt->status === 'in_progress')
                                        <span class="status-indicator bg-warning"></span>
                                        <span class="badge bg-warning">In Progress</span>
                                    @elseif($attempt->status === 'expired')
                                        <span class="status-indicator bg-danger"></span>
                                        <span class="badge bg-secondary">Expired</span>
                                    @else
                                        <span class="status-indicator bg-light"></span>
                                        <span class="badge bg-light text-dark">{{ ucfirst($attempt->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attempt->status === 'completed' && $attempt->percentage !== null)
                                        <div class="fw-bold text-primary">{{ number_format($attempt->percentage, 1) }}%</div>
                                        <small class="text-muted">{{ $attempt->obtained_marks }}/{{ $attempt->total_marks }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attempt->status === 'completed')
                                        @php
                                            $passed = $attempt->percentage >= ($attempt->entryTest->passing_score ?? 0);
                                        @endphp
                                        @if($passed)
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
                                    @if($attempt->completed_at && $attempt->started_at)
                                        @php
                                            $duration = $attempt->started_at->diffInMinutes($attempt->completed_at);
                                        @endphp
                                        <span class="fw-bold">{{ $duration }} min</span>
                                    @elseif($attempt->started_at)
                                        @php
                                            $elapsed = $attempt->started_at->diffInMinutes(now());
                                        @endphp
                                        <span class="text-warning">{{ $elapsed }} min (ongoing)</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $attempt->created_at->format('M j, Y') }}</div>
                                    <small class="text-muted">{{ $attempt->created_at->format('g:i A') }}</small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.student-attempts.show', $attempt) }}" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($attempt->status === 'completed' && !($attempt->student->is_retake_allowed ?? false))
                                            <form method="POST" action="{{ route('admin.student-attempts.allow-retake', $attempt) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-warning" 
                                                        title="Allow Retake"
                                                        onclick="return confirm('Allow this student to retake the test?')">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form method="POST" action="{{ route('admin.student-attempts.destroy', $attempt) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Delete Attempt"
                                                    onclick="return confirm('Are you sure you want to delete this attempt? This action cannot be undone.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($attempts->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $attempts->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Attempts Found</h5>
                    <p class="text-muted">No student attempts match your current filters.</p>
                    <a href="{{ route('admin.student-attempts.index') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-2"></i>Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Export functionality
        function exportAttempts() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'csv');
            window.location.href = `{{ route('admin.student-attempts.index') }}?${params.toString()}`;
        }

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

        // Search on Enter
        document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });

        // Real-time status updates (optional)
        setInterval(function() {
            // You can add AJAX calls here to update in-progress attempts
            // This would be useful for live monitoring of ongoing tests
        }, 30000); // Update every 30 seconds
    </script>
</body>
</html>