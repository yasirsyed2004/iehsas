<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $student->full_name }} - Student Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('admin.partials.styles')
    <style>
        .info-card {
            border-left: 4px solid #6c757d;
        }
        .info-card.primary { border-left-color: #0d6efd; }
        .info-card.success { border-left-color: #198754; }
        .info-card.warning { border-left-color: #ffc107; }
        .info-card.danger { border-left-color: #dc3545; }
        .info-card.info { border-left-color: #0dcaf0; }
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #6c757d;
            border: 2px solid #fff;
        }
        .timeline-item.success::before { background: #198754; }
        .timeline-item.danger::before { background: #dc3545; }
        .timeline-item.warning::before { background: #ffc107; }
        .timeline-item.primary::before { background: #0d6efd; }
    </style>
</head>
<body>
    @include('admin.partials.sidebar', ['activeMenu' => 'registered-students'])

    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-user me-2"></i>{{ $student->full_name }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.registered-students.index') }}">Registered Students</a></li>
                        <li class="breadcrumb-item active">{{ $student->full_name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <form action="{{ route('admin.registered-students.toggle-retake', $student) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-{{ $student->is_retake_allowed ? 'warning' : 'success' }}">
                        <i class="fas fa-redo me-1"></i>
                        {{ $student->is_retake_allowed ? 'Disallow Retake' : 'Allow Retake' }}
                    </button>
                </form>
                <a href="{{ route('admin.registered-students.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card info-card {{ $testStats['passed'] ? 'success' : 'danger' }}">
                    <div class="card-body text-center">
                        <h3 class="mb-0 {{ $testStats['passed'] ? 'text-success' : 'text-danger' }}">
                            {{ $testStats['passed'] ? 'PASSED' : 'NOT PASSED' }}
                        </h3>
                        <small class="text-muted">Test Result</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card info-card primary">
                    <div class="card-body text-center">
                        <h3 class="mb-0 text-primary">{{ number_format($testStats['best_score'], 1) }}%</h3>
                        <small class="text-muted">Best Score</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card info-card info">
                    <div class="card-body text-center">
                        <h3 class="mb-0 text-info">{{ $testStats['total_attempts'] }}</h3>
                        <small class="text-muted">Total Attempts</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card info-card warning">
                    <div class="card-body text-center">
                        <h3 class="mb-0">
                            @if($student->enrollment_status === 'completed')
                                <span class="text-success"><i class="fas fa-check-circle"></i></span>
                            @elseif($student->enrollment_status === 'in_progress')
                                <span class="text-info"><i class="fas fa-spinner"></i></span>
                            @else
                                <span class="text-warning"><i class="fas fa-clock"></i></span>
                            @endif
                        </h3>
                        <small class="text-muted">{{ $student->enrollment_status_label }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Personal Information -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-user-circle me-2"></i>Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="40%">Full Name:</th>
                                <td>{{ $student->full_name }}</td>
                            </tr>
                            <tr>
                                <th>Father's Name:</th>
                                <td>{{ $student->father_name }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><a href="mailto:{{ $student->email }}">{{ $student->email }}</a></td>
                            </tr>
                            <tr>
                                <th>Contact:</th>
                                <td>{{ $student->contact_number }}</td>
                            </tr>
                            <tr>
                                <th>Date of Birth:</th>
                                <td>{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Gender:</th>
                                <td>{{ ucfirst($student->gender) }}</td>
                            </tr>
                            <tr>
                                <th>Nationality:</th>
                                <td>{{ $student->nationality }}</td>
                            </tr>
                            <tr>
                                <th>{{ $student->id_type_label }}:</th>
                                <td><code>{{ $student->formatted_id }}</code></td>
                            </tr>
                            <tr>
                                <th>Qualification:</th>
                                <td>{{ $student->qualification }}</td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td>{{ $student->home_address ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Registered At:</th>
                                <td>{{ $student->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Retake Allowed:</th>
                                <td>
                                    @if($student->is_retake_allowed)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Selected Courses -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-graduation-cap me-2"></i>Selected Courses</h5>
                    </div>
                    <div class="card-body">
                        @if($student->selectedCourses->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($student->selectedCourses as $course)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <i class="fas fa-book text-primary me-2"></i>
                                        {{ $course->title }}
                                    </div>
                                    <span class="badge bg-{{ $course->status === 'published' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-3 text-muted">
                                <i class="fas fa-book fa-2x mb-2"></i>
                                <p class="mb-0">No courses selected</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Documents Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-file-alt me-2"></i>Documents Status</h5>
                    </div>
                    <div class="card-body">
                        @if($documentSummary['total'] > 0)
                            <div class="row text-center">
                                <div class="col-4">
                                    <h4 class="text-warning mb-0">{{ $documentSummary['pending'] }}</h4>
                                    <small class="text-muted">Pending</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-success mb-0">{{ $documentSummary['approved'] }}</h4>
                                    <small class="text-muted">Approved</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-danger mb-0">{{ $documentSummary['rejected'] }}</h4>
                                    <small class="text-muted">Rejected</small>
                                </div>
                            </div>
                            <hr>
                            <div class="list-group list-group-flush">
                                @foreach($student->enrollmentDocuments as $doc)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <i class="fas fa-file me-2"></i>
                                        {{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}
                                        @if($doc->sub_type)
                                            <small class="text-muted">({{ ucfirst(str_replace('_', ' ', $doc->sub_type)) }})</small>
                                        @endif
                                    </div>
                                    <span class="badge bg-{{ $doc->status === 'approved' ? 'success' : ($doc->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($doc->status) }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-2"></i>
                                <p class="mb-0">No documents uploaded yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Attempts History -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Test Attempts History</h5>
            </div>
            <div class="card-body">
                @if($student->entryTestAttempts->count() > 0)
                    <div class="timeline">
                        @foreach($student->entryTestAttempts as $attempt)
                        <div class="timeline-item {{ $attempt->status === 'completed' ? ($attempt->passed ? 'success' : 'danger') : 'warning' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $attempt->entryTest->title ?? 'Entry Test' }}</h6>
                                    <p class="mb-1 text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $attempt->created_at->format('M d, Y H:i') }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    @if($attempt->status === 'completed')
                                        <span class="badge bg-{{ $attempt->passed ? 'success' : 'danger' }} mb-1">
                                            {{ $attempt->passed ? 'PASSED' : 'FAILED' }}
                                        </span>
                                        <div class="h5 mb-0 {{ $attempt->passed ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($attempt->percentage, 1) }}%
                                        </div>
                                        <small class="text-muted">
                                            {{ $attempt->total_score }} / {{ $attempt->entryTest->questions->count() ?? '?' }} correct
                                        </small>
                                    @elseif($attempt->status === 'in_progress')
                                        <span class="badge bg-warning">In Progress</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($attempt->status) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                        <h5>No test attempts yet</h5>
                        <p>Student hasn't attempted any entry test.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
