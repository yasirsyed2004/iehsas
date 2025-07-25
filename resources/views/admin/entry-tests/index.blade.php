{{-- File: resources/views/admin/entry-tests/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entry Tests Management - Admin LMS</title>
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
        .status-badge {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }
        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .table-responsive {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
                <h2><i class="fas fa-clipboard-list me-2"></i>Entry Tests Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Entry Tests</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.entry-tests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create New Test
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

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="h4 text-primary mb-1">{{ $entryTests->total() }}</div>
                    <div class="text-muted">Total Tests</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="h4 text-success mb-1">{{ $entryTests->where('is_active', true)->count() }}</div>
                    <div class="text-muted">Active Tests</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="h4 text-info mb-1">{{ $entryTests->sum('questions_count') }}</div>
                    <div class="text-muted">Total Questions</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="h4 text-warning mb-1">{{ $entryTests->sum('attempts_count') }}</div>
                    <div class="text-muted">Total Attempts</div>
                </div>
            </div>
        </div>

        <!-- Entry Tests Table -->
        <div class="table-responsive">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Entry Tests List</h5>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;" onchange="filterTests(this.value)">
                        <option value="all">All Tests</option>
                        <option value="active">Active Only</option>
                        <option value="inactive">Inactive Only</option>
                    </select>
                </div>
            </div>

            @if($entryTests->count() > 0)
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Duration</th>
                            <th>Questions</th>
                            <th>Passing Score</th>
                            <th>Attempts</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entryTests as $test)
                            <tr data-status="{{ $test->is_active ? 'active' : 'inactive' }}">
                                <td class="fw-bold">#{{ $test->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $test->title }}</div>
                                    <small class="text-muted">{{ Str::limit($test->description, 50) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $test->duration_minutes }} min</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $test->questions_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">{{ $test->passing_score }}%</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $test->attempts_count ?? 0 }}</span>
                                </td>
                                <td>
                                    @if($test->is_active)
                                        <span class="badge bg-success status-badge">Active</span>
                                    @else
                                        <span class="badge bg-danger status-badge">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $test->created_at->format('M j, Y') }}</small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.entry-tests.show', $test) }}" class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.entry-tests.edit', $test) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.entry-tests.toggle-status', $test) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-sm {{ $test->is_active ? 'btn-secondary' : 'btn-success' }}" 
                                                    title="{{ $test->is_active ? 'Deactivate' : 'Activate' }}"
                                                    onclick="return confirm('Are you sure you want to {{ $test->is_active ? 'deactivate' : 'activate' }} this test?')">
                                                <i class="fas fa-{{ $test->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        @if($test->attempts_count == 0)
                                            <form method="POST" action="{{ route('admin.entry-tests.destroy', $test) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this entry test? This action cannot be undone.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-danger" 
                                                    title="Cannot delete - has attempts" 
                                                    disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($entryTests->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $entryTests->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Entry Tests Found</h5>
                    <p class="text-muted">Create your first entry test to get started.</p>
                    <a href="{{ route('admin.entry-tests.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create First Test
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterTests(status) {
            const rows = document.querySelectorAll('tbody tr[data-status]');
            
            rows.forEach(row => {
                if (status === 'all') {
                    row.style.display = '';
                } else {
                    row.style.display = row.dataset.status === status ? '' : 'none';
                }
            });
        }

        // Auto-hide alerts after 5 seconds
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