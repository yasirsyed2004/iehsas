{{-- File: resources/views/admin/dashboard/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .user-info {
            color: #ecf0f1;
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .mini-stat {
            background: white;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="user-info text-center">
            <div class="mb-2">
                @if($admin && $admin->avatar)
                    <img src="{{ asset('storage/' . $admin->avatar) }}" alt="Avatar" class="rounded-circle" width="60" height="60">
                @else
                    <i class="fas fa-user-circle fa-3x"></i>
                @endif
            </div>
            <h6>{{ $admin ? $admin->name : 'Admin' }}</h6>
            <small>{{ $admin ? ucfirst(str_replace('_', ' ', $admin->role)) : 'Administrator' }}</small>
        </div>
        
        <ul class="nav flex-column p-3">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.dashboard') }}">
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
                <a class="nav-link" href="#" onclick="alert('Coming Soon!')">
                    <i class="fas fa-book me-2"></i> E-Learning
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
        <!-- Top Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </h2>
                    <div class="text-muted">
                        <i class="fas fa-calendar-alt me-2"></i>
                        {{ now()->format('l, M d, Y') }}
                    </div>
                </div>
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

        <!-- Overview Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(45deg, #3498db, #2980b9);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                            <p class="text-muted mb-0">Total Users</p>
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> {{ $stats['active_users'] }} Active
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(45deg, #27ae60, #2ecc71);">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $stats['total_students'] }}</h3>
                            <p class="text-muted mb-0">Registered Students</p>
                            <small class="text-info">
                                <i class="fas fa-plus-circle"></i> {{ $stats['today_attempts'] }} Today
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(45deg, #e74c3c, #c0392b);">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $stats['total_tests'] }}</h3>
                            <p class="text-muted mb-0">Entry Tests</p>
                            <small class="text-warning">
                                <i class="fas fa-eye"></i> {{ $stats['active_tests'] }} Active
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(45deg, #f39c12, #e67e22);">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $stats['total_attempts'] }}</h3>
                            <p class="text-muted mb-0">Test Attempts</p>
                            <small class="text-primary">
                                <i class="fas fa-percentage"></i> {{ $stats['average_score'] }}% Avg
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Stats -->
        <div class="row mb-4">
            <div class="col-lg-3 col-6 mb-3">
                <div class="mini-stat">
                    <h4 class="text-success mb-1">{{ $stats['completed_attempts'] }}</h4>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
            <div class="col-lg-3 col-6 mb-3">
                <div class="mini-stat">
                    <h4 class="text-primary mb-1">{{ $stats['passed_attempts'] }}</h4>
                    <small class="text-muted">Passed</small>
                </div>
            </div>
            <div class="col-lg-3 col-6 mb-3">
                <div class="mini-stat">
                    <h4 class="text-warning mb-1">{{ $stats['in_progress_attempts'] }}</h4>
                    <small class="text-muted">In Progress</small>
                </div>
            </div>
            <div class="col-lg-3 col-6 mb-3">
                <div class="mini-stat">
                    <h4 class="text-info mb-1">{{ $stats['this_week_attempts'] }}</h4>
                    <small class="text-muted">This Week</small>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-4">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-area me-2"></i>Test Attempts (Last 7 Days)
                    </h5>
                    <canvas id="attemptsChart" height="100"></canvas>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-pie me-2"></i>Pass Rate by Test
                    </h5>
                    @if($testStats->count() > 0)
                        @foreach($testStats as $test)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="fw-bold">{{ Str::limit($test->title, 20) }}</small>
                                    <small class="text-muted">
                                        {{ $test->completed_attempts > 0 ? round(($test->passed_attempts / $test->completed_attempts) * 100, 1) : 0 }}%
                                    </small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $test->completed_attempts > 0 ? ($test->passed_attempts / $test->completed_attempts) * 100 : 0 }}%"></div>
                                </div>
                                <small class="text-muted">{{ $test->passed_attempts }}/{{ $test->completed_attempts }} passed</small>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No test data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Attempts and Quick Actions -->
        <div class="row">
            <!-- Recent Attempts -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock me-2"></i>Recent Test Attempts
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($recentAttempts && $recentAttempts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>Test</th>
                                            <th>Status</th>
                                            <th>Score</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentAttempts as $attempt)
                                        <tr>
                                            <td>
                                                <div>
                                                    @if($attempt->student)
                                                        <strong class="small">{{ $attempt->student->full_name }}</strong><br>
                                                        <small class="text-muted">{{ $attempt->student->cnic }}</small>
                                                    @else
                                                        <strong class="small text-danger">Student Not Found</strong><br>
                                                        <small class="text-muted">ID: {{ $attempt->student_id ?? $attempt->user_id }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="small">
                                                @if($attempt->entryTest)
                                                    {{ Str::limit($attempt->entryTest->title, 25) }}
                                                @else
                                                    <span class="text-muted">Test Deleted</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attempt->status === 'completed')
                                                    @if($attempt->percentage !== null && $attempt->entryTest && $attempt->percentage >= $attempt->entryTest->passing_score)
                                                        <span class="badge bg-success">Passed</span>
                                                    @else
                                                        <span class="badge bg-danger">Failed</span>
                                                    @endif
                                                @elseif($attempt->status === 'in_progress')
                                                    <span class="badge bg-warning">In Progress</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($attempt->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attempt->percentage !== null)
                                                    <strong>{{ round($attempt->percentage, 1) }}%</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="small">{{ $attempt->created_at->format('M d, H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.student-attempts.show', $attempt) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No recent attempts</h5>
                                <p class="text-muted">Test attempts will appear here once students start taking tests.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add New User
                            </a>
                            <a href="{{ route('admin.entry-tests.create') }}" class="btn btn-success">
                                <i class="fas fa-clipboard-list me-2"></i>Create Entry Test
                            </a>
                            <a href="{{ route('admin.questions.create') }}" class="btn btn-info">
                                <i class="fas fa-question me-2"></i>Add Question
                            </a>
                            <a href="{{ route('admin.student-attempts.index') }}" class="btn btn-warning">
                                <i class="fas fa-chart-bar me-2"></i>View All Attempts
                            </a>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-server me-2"></i>System Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Database</span>
                            <span class="badge bg-success">Online</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Entry Tests</span>
                            <span class="badge bg-{{ $stats['active_tests'] > 0 ? 'success' : 'warning' }}">
                                {{ $stats['active_tests'] > 0 ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Students</span>
                            <span class="badge bg-info">{{ $stats['total_students'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Today's Attempts</span>
                            <span class="badge bg-primary">{{ $stats['today_attempts'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script>
        // Attempts Chart
        const ctx = document.getElementById('attemptsChart').getContext('2d');
        const chartData = @json($chartData);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(item => item.date),
                datasets: [{
                    label: 'Attempts',
                    data: chartData.map(item => item.attempts),
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>