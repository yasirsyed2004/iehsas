<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Students - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.partials.styles')
    <style>
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-card.total { border-left-color: #6c757d; }
        .stat-card.passed { border-left-color: #28a745; }
        .stat-card.failed { border-left-color: #dc3545; }
        .stat-card.eligible { border-left-color: #17a2b8; }
        .stat-card.enrolled { border-left-color: #6f42c1; }
        .status-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 500;
        }
        .test-badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar', ['activeMenu' => 'registered-students'])

    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-user-graduate me-2"></i>Registered Students</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Registered Students</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.registered-students.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-export me-2"></i>Export CSV
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md">
                <div class="card stat-card total">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                <small class="text-muted">Total Registered</small>
                            </div>
                            <i class="fas fa-users fa-2x text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md">
                <div class="card stat-card passed">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0 text-success">{{ $stats['passed'] }}</h3>
                                <small class="text-muted">Test Passed</small>
                            </div>
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md">
                <div class="card stat-card failed">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0 text-danger">{{ $stats['failed'] }}</h3>
                                <small class="text-muted">Test Failed</small>
                            </div>
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md">
                <div class="card stat-card eligible">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0 text-info">{{ $stats['eligible'] }}</h3>
                                <small class="text-muted">Eligible</small>
                            </div>
                            <i class="fas fa-user-check fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md">
                <div class="card stat-card enrolled">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0 text-purple" style="color: #6f42c1;">{{ $stats['enrolled'] }}</h3>
                                <small class="text-muted">Enrolled</small>
                            </div>
                            <i class="fas fa-graduation-cap fa-2x" style="color: #6f42c1;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.registered-students.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select class="form-select" name="test_status">
                                <option value="">All Test Status</option>
                                <option value="passed" {{ request('test_status') == 'passed' ? 'selected' : '' }}>Passed</option>
                                <option value="failed" {{ request('test_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="in_progress" {{ request('test_status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="not_attempted" {{ request('test_status') == 'not_attempted' ? 'selected' : '' }}>Not Attempted</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="enrollment_status">
                                <option value="">All Enrollment Status</option>
                                <option value="eligible" {{ request('enrollment_status') == 'eligible' ? 'selected' : '' }}>Eligible</option>
                                <option value="code_sent" {{ request('enrollment_status') == 'code_sent' ? 'selected' : '' }}>Code Sent</option>
                                <option value="in_progress" {{ request('enrollment_status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('enrollment_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Search by name, email, phone, ID..."
                                   name="search" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>All Registered Students ({{ $students->total() }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Contact</th>
                                <th>ID Info</th>
                                <th>Test Result</th>
                                <th>Enrollment Status</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($student->full_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $student->full_name }}</div>
                                            <small class="text-muted">{{ $student->father_name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><i class="fas fa-envelope text-muted me-1"></i>{{ $student->email }}</div>
                                    <small class="text-muted"><i class="fas fa-phone me-1"></i>{{ $student->contact_number }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $student->id_type_label }}</span>
                                    <div class="small text-muted">{{ $student->formatted_id }}</div>
                                </td>
                                <td>
                                    @if($student->latestAttempt)
                                        @if($student->latestAttempt->status === 'completed')
                                            @if($student->latestAttempt->passed)
                                                <span class="test-badge bg-success text-white">
                                                    <i class="fas fa-check me-1"></i>Passed
                                                </span>
                                                <div class="small text-success fw-bold">{{ number_format($student->latestAttempt->percentage, 1) }}%</div>
                                            @else
                                                <span class="test-badge bg-danger text-white">
                                                    <i class="fas fa-times me-1"></i>Failed
                                                </span>
                                                <div class="small text-danger">{{ number_format($student->latestAttempt->percentage, 1) }}%</div>
                                            @endif
                                        @else
                                            <span class="test-badge bg-warning text-dark">
                                                <i class="fas fa-spinner me-1"></i>In Progress
                                            </span>
                                        @endif
                                    @else
                                        <span class="test-badge bg-secondary text-white">Not Attempted</span>
                                    @endif
                                </td>
                                <td>
                                    @php $enrollmentStatus = $student->enrollment_status; @endphp
                                    @switch($enrollmentStatus)
                                        @case('completed')
                                            <span class="status-badge bg-success text-white">
                                                <i class="fas fa-check-circle me-1"></i>Enrolled
                                            </span>
                                            @break
                                        @case('in_progress')
                                            <span class="status-badge bg-info text-white">
                                                <i class="fas fa-spinner me-1"></i>In Progress
                                            </span>
                                            @break
                                        @case('code_sent')
                                            <span class="status-badge bg-primary text-white">
                                                <i class="fas fa-paper-plane me-1"></i>Code Sent
                                            </span>
                                            @break
                                        @case('eligible')
                                            <span class="status-badge bg-warning text-dark">
                                                <i class="fas fa-user-check me-1"></i>Eligible
                                            </span>
                                            @break
                                        @case('code_expired')
                                            <span class="status-badge bg-secondary text-white">
                                                <i class="fas fa-clock me-1"></i>Code Expired
                                            </span>
                                            @break
                                        @default
                                            <span class="status-badge bg-light text-dark">
                                                <i class="fas fa-minus me-1"></i>Not Eligible
                                            </span>
                                    @endswitch
                                </td>
                                <td>
                                    <div>{{ $student->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $student->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.registered-students.show', $student) }}"
                                           class="btn btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.registered-students.toggle-retake', $student) }}"
                                              method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-outline-{{ $student->is_retake_allowed ? 'warning' : 'success' }}"
                                                    title="{{ $student->is_retake_allowed ? 'Disallow Retake' : 'Allow Retake' }}">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </form>
                                        @if(!$student->enrollment_form_submitted)
                                        <button class="btn btn-outline-danger" onclick="deleteStudent({{ $student->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-user-graduate fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No registered students found</h5>
                                    <p class="text-muted">Students will appear here after registering through the entry test form.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center py-3 px-3">
                    <div class="text-muted small">
                        Showing {{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} students
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        @if($students->hasPages())
                            {{ $students->withQueryString()->links('pagination::bootstrap-5') }}
                        @endif
                        @if(request('per_page') != 'all')
                            <a href="{{ route('admin.registered-students.index', array_merge(request()->query(), ['per_page' => 'all'])) }}"
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-list me-1"></i>View All
                            </a>
                        @else
                            <a href="{{ route('admin.registered-students.index', array_merge(request()->except('per_page'), [])) }}"
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-th-list me-1"></i>Paginate
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteStudent(id) {
            if (confirm('Are you sure you want to delete this student? This will also delete their test attempts and documents.')) {
                const form = document.getElementById('delete-form');
                form.action = `/admin/registered-students/${id}`;
                form.submit();
            }
        }

        setTimeout(() => {
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
