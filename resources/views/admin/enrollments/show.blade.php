{{-- resources/views/admin/enrollments/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Enrollment Details - {{ $student->full_name }} - Admin LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --danger-gradient: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
        }
        
        .sidebar .nav-link {
            color: #ecf0f1;
            border-radius: 8px;
            margin: 3px 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.15);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .user-info {
            color: #ecf0f1;
            padding: 25px 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        /* Enhanced Cards */
        .card-modern {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            border: none;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 25px;
        }

        .card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.12);
        }

        .card-header-gradient {
            background: var(--primary-gradient);
            color: white;
            padding: 20px 30px;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        /* Status Cards */
        .status-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            min-height: 180px;
            display: flex;
            flex-direction: column;
        }

        .status-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
        }

        .status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 35px rgba(0,0,0,0.12);
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1rem;
            color: #2c3e50;
            font-weight: 600;
        }

        /* Course Cards */
        .course-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 15px;
            padding: 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--primary-gradient);
            transition: width 0.3s ease;
        }

        .course-card:hover {
            border-color: #667eea;
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.15);
            transform: translateY(-3px);
        }

        .course-card:hover::before {
            width: 100%;
            opacity: 0.05;
        }

        .course-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }

        /* Document Cards */
        .document-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .document-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            transform: translateY(-3px);
        }

        /* Buttons */
        .btn-modern {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }

        .btn-primary-gradient {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary-modern {
            background: #6c757d;
            color: white;
        }

        .btn-secondary-modern:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        /* Entry Test History */
        .test-attempt-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 12px;
            padding: 15px;
            border-left: 4px solid #667eea;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .test-attempt-item:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transform: translateX(5px);
        }

        /* Header */
        .page-header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        /* Level Badges */
        .level-badge-beginner {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .level-badge-intermediate {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .level-badge-advanced {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Modal Enhancement */
        .modal-modern .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .modal-modern .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 20px 30px;
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
            <h6 class="mb-1">{{ Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : 'Admin' }}</h6>
            <small style="opacity: 0.8;">{{ Auth::guard('admin')->check() ? ucfirst(str_replace('_', ' ', Auth::guard('admin')->user()->role ?? 'admin')) : 'Administrator' }}</small>
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
                <a class="nav-link" href="{{ route('admin.student-attempts.index') }}">
                    <i class="fas fa-clipboard-check me-2"></i> Student Attempts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.courses.index') }}">
                    <i class="fas fa-graduation-cap me-2"></i> Courses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.course-categories.index') }}">
                    <i class="fas fa-tags me-2"></i> Course Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.enrollments.index') }}">
                    <i class="fas fa-user-graduate me-2"></i> Enrollments
                </a>
            </li>
        </ul>
        
        <!-- Logout -->
        <div class="p-3 mt-auto">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm w-100">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="page-header animate-fade-in">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-user-graduate me-2"></i>{{ $student->full_name }}
                    </h1>
                    <p class="text-muted mb-0">Review student information and documents</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary-modern btn-modern">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                    @if($student->isEligibleForEnrollment())
                        <form method="POST" action="{{ route('admin.enrollments.send-form', $student) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary-gradient btn-modern"
                                    onclick="return confirm('Send enrollment form to {{ $student->email }}?')">
                                <i class="fas fa-paper-plane me-2"></i>Send Form
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Cards -->
        <div class="row g-4 mb-4 animate-fade-in">
            <!-- Enrollment Status -->
            <div class="col-md-4">
                <div class="status-card">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 50px; height: 50px; background: var(--primary-gradient); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-clipboard-check text-white fs-4"></i>
                        </div>
                        <h5 class="ms-3 mb-0 fw-bold">Enrollment Status</h5>
                    </div>
                    @php
                        $statusColors = [
                            'not_eligible' => 'bg-secondary',
                            'eligible' => 'bg-primary',
                            'code_sent' => 'bg-warning',
                            'code_expired' => 'bg-danger',
                            'in_progress' => 'bg-info',
                            'completed' => 'bg-success',
                        ];
                        $statusColor = $statusColors[$student->enrollment_status] ?? 'bg-secondary';
                    @endphp
                    <span class="status-badge {{ $statusColor }} text-white">
                        <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                        {{ $student->enrollment_status_label }}
                    </span>
                    @if($student->enrollment_code_generated_at)
                        <p class="text-muted small mb-0 mt-3">
                            <i class="far fa-calendar-alt me-1"></i>
                            Code sent: {{ $student->enrollment_code_generated_at->format('M j, Y g:i A') }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Entry Test Status -->
            <div class="col-md-4">
                <div class="status-card">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 50px; height: 50px; background: var(--success-gradient); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-file-alt text-white fs-4"></i>
                        </div>
                        <h5 class="ms-3 mb-0 fw-bold">Entry Test</h5>
                    </div>
                    @if($student->latestAttempt)
                        @if($student->hasPassedEntryTest())
                            <span class="status-badge bg-success text-white">
                                <i class="fas fa-check-circle"></i>
                                Passed ({{ $student->latestAttempt->percentage }}%)
                            </span>
                        @else
                            <span class="status-badge bg-danger text-white">
                                <i class="fas fa-times-circle"></i>
                                Failed ({{ $student->latestAttempt->percentage }}%)
                            </span>
                        @endif
                        <p class="text-muted small mb-0 mt-3">
                            <i class="far fa-calendar-alt me-1"></i>
                            Completed: {{ $student->latestAttempt->created_at->format('M j, Y') }}
                        </p>
                    @else
                        <span class="status-badge bg-secondary text-white">
                            <i class="fas fa-minus-circle"></i>
                            Not Attempted
                        </span>
                    @endif
                </div>
            </div>

            <!-- Documents Status -->
            <div class="col-md-4">
                <div class="status-card">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 50px; height: 50px; background: var(--warning-gradient); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-folder-open text-white fs-4"></i>
                        </div>
                        <h5 class="ms-3 mb-0 fw-bold">Documents</h5>
                    </div>
                    @if($student->enrollmentDocuments->count() > 0)
                        @php $docStatus = $student->documents_status; @endphp
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>{{ $docStatus['approved'] ?? 0 }}
                            </span>
                            <span class="badge bg-warning">
                                <i class="fas fa-clock me-1"></i>{{ $docStatus['pending'] ?? 0 }}
                            </span>
                            <span class="badge bg-danger">
                                <i class="fas fa-times me-1"></i>{{ $docStatus['rejected'] ?? 0 }}
                            </span>
                        </div>
                        <p class="text-muted small mb-0 mt-3">
                            Total: {{ $docStatus['total'] ?? 0 }} documents
                        </p>
                    @else
                        <span class="text-muted">No documents submitted</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Personal Information & Entry Test -->
        <div class="row g-4 mb-4">
            <!-- Personal Information -->
            <div class="col-lg-6">
                <div class="card-modern">
                    <div class="card-header-gradient">
                        <i class="fas fa-user me-2"></i>Personal Information
                    </div>
                    <div class="card-body p-4">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Full Name</div>
                                <div class="info-value">{{ $student->full_name }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Father's Name</div>
                                <div class="info-value">{{ $student->father_name ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value text-truncate">{{ $student->email }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Phone</div>
                                <div class="info-value">{{ $student->contact_number }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Date of Birth</div>
                                <div class="info-value">{{ $student->date_of_birth ? $student->date_of_birth->format('M j, Y') : 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Gender</div>
                                <div class="info-value">{{ ucfirst($student->gender) }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">{{ ucfirst($student->id_type) }}</div>
                                <div class="info-value">{{ $student->formatted_id }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Nationality</div>
                                <div class="info-value">{{ $student->nationality ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Qualification</div>
                                <div class="info-value">{{ $student->qualification }}</div>
                            </div>
                        </div>
                        @if($student->home_address)
                        <div class="info-item mt-3">
                            <div class="info-label">Home Address</div>
                            <div class="info-value">{{ $student->home_address }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Entry Test History -->
            <div class="col-lg-6">
                <div class="card-modern">
                    <div class="card-header-gradient">
                        <i class="fas fa-history me-2"></i>Entry Test History
                    </div>
                    <div class="card-body p-4">
                        @if($student->entryTestAttempts->count() > 0)
                            @foreach($student->entryTestAttempts->take(5) as $attempt)
                                <div class="test-attempt-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $attempt->percentage }}%</h6>
                                            <small class="text-muted">
                                                <i class="far fa-clock me-1"></i>
                                                {{ $attempt->created_at->format('M j, Y g:i A') }}
                                            </small>
                                        </div>
                                        <div>
                                            @if($attempt->percentage >= 60)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Passed
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Failed
                                                </span>
                                            @endif
                                            <span class="badge bg-primary ms-1">{{ $attempt->status }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox text-muted fa-3x mb-3"></i>
                                <p class="text-muted">No entry test attempts found.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Selected Courses -->
        @if($student->selectedCourses && $student->selectedCourses->count() > 0)
        <div class="card-modern animate-fade-in">
            <div class="card-header-gradient">
                <i class="fas fa-graduation-cap me-2"></i>Selected Courses
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @foreach($student->selectedCourses as $course)
                        <div class="col-md-6">
                            <div class="course-card">
                                <div class="d-flex align-items-start">
                                    <div class="course-icon me-3">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="fw-bold mb-2">{{ $course->title }}</h5>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge level-badge-{{ $course->level }}">
                                                <i class="fas fa-layer-group me-1"></i>
                                                {{ ucfirst($course->level) }}
                                            </span>
                                            @if($course->pivot && $course->pivot->selected_at)
                                                <small class="text-muted">
                                                    <i class="far fa-calendar-check me-1"></i>
                                                    {{ \Carbon\Carbon::parse($course->pivot->selected_at)->format('M d, Y') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="alert alert-info border-0 mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Total <strong>{{ $student->selectedCourses->count() }}</strong> course(s) selected during registration
                </div>
            </div>
        </div>
        @endif

        <!-- Documents Section -->
        @if($student->enrollmentDocuments->count() > 0)
        <div class="card-modern animate-fade-in mt-4">
            <div class="card-header-gradient">
                <i class="fas fa-file-alt me-2"></i>Submitted Documents
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    @foreach($student->enrollmentDocuments as $document)
                        <div class="col-md-6">
                            <div class="document-card">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $document->document_type_label }}</h6>
                                        <p class="text-muted small mb-1">{{ $document->original_filename }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-file me-1"></i>{{ $document->formatted_file_size }} 
                                            <i class="fas fa-calendar ms-2 me-1"></i>{{ $document->uploaded_at->format('M j, Y') }}
                                        </small>
                                    </div>
                                    <span class="badge {{ $document->status === 'approved' ? 'bg-success' : ($document->status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ $document->status_label }}
                                    </span>
                                </div>

                                @if($document->admin_comments)
                                    <div class="alert alert-secondary border-0 py-2 px-3 small mb-3">
                                        <strong>Admin:</strong> {{ $document->admin_comments }}
                                    </div>
                                @endif

                                <div class="d-flex gap-2">
                                    <a href="{{ $document->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="{{ $document->download_url }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>

                                    @if($document->status === 'pending')
                                        <form method="POST" action="{{ route('admin.enrollments.document.approve', $document) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success"
                                                    onclick="return confirm('Approve this document?')">
                                                <i class="fas fa-check me-1"></i>Approve
                                            </button>
                                        </form>
                                        
                                        <button onclick="openRejectModal({{ $document->id }})" class="btn btn-sm btn-danger">
                                            <i class="fas fa-times me-1"></i>Reject
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Reject Document Modal -->
    <div class="modal fade modal-modern" id="rejectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-times-circle me-2"></i>Reject Document
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label for="comments" class="form-label fw-bold">Reason for rejection *</label>
                        <textarea name="comments" id="comments" rows="4" required
                                  class="form-control" placeholder="Please provide specific feedback..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary-modern btn-modern" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-modern">
                            <i class="fas fa-times me-2"></i>Reject Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- PDF Download Section - ADD AT THE VERY END --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                <h5 class="mb-3 fw-bold">Download Complete Enrollment Report</h5>
                <p class="text-muted mb-4">
                    Generate a comprehensive PDF including student details, entry test history, 
                    selected courses, and all submitted documents.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('admin.enrollments.pdf.download', $student) }}" 
                       class="btn btn-danger btn-lg px-5">
                        <i class="fas fa-download me-2"></i>
                        Download PDF Report
                    </a>
                    <a href="{{ route('admin.enrollments.pdf.view', $student) }}" 
                       class="btn btn-outline-danger btn-lg px-5"
                       target="_blank">
                        <i class="fas fa-eye me-2"></i>
                        View in Browser
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openRejectModal(documentId) {
            const form = document.getElementById('rejectForm');
            form.action = `/admin/enrollments/document/${documentId}/reject`;
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }
    </script>
</body>
</html>