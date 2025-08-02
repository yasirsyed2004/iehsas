{{-- File: resources/views/admin/courses/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} - Admin Dashboard</title>
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
        .course-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        .course-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: translate(50px, -50px);
        }
        .course-thumbnail {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 3px solid rgba(255,255,255,0.3);
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-3px);
        }
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            margin-bottom: 15px;
        }
        .badge-large {
            font-size: 0.875rem;
            padding: 8px 16px;
        }
        .module-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .module-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .lesson-item {
            padding: 10px 15px;
            border-bottom: 1px solid #f1f3f4;
            transition: background-color 0.3s;
        }
        .lesson-item:hover {
            background-color: #f8f9fa;
        }
        .lesson-item:last-child {
            border-bottom: none;
        }
        .lesson-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            margin-right: 12px;
        }
        .action-button {
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .info-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f3f4;
        }
        .info-table tr:last-child td {
            border-bottom: none;
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
                <a class="nav-link" href="{{ route('admin.student-attempts.index') }}">
                    <i class="fas fa-chart-line me-2"></i> Student Attempts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.courses.index') }}">
                    <i class="fas fa-graduation-cap me-2"></i> Courses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.course-categories.index') }}">
                    <i class="fas fa-tags me-2"></i> Course Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.enrollments.index') }}">
                    <i class="fas fa-user-graduate me-2"></i> Enrollments
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
                <h2><i class="fas fa-graduation-cap me-2"></i>Course Details</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
                        <li class="breadcrumb-item active">{{ $course->title }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-warning action-button">
                    <i class="fas fa-edit me-1"></i>Edit Course
                </a>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary action-button">
                    <i class="fas fa-arrow-left me-1"></i>Back to Courses
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

        <!-- Course Header -->
        <div class="course-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="course-thumbnail me-4">
                        @else
                            <div class="course-thumbnail me-4 bg-white bg-opacity-25 d-flex align-items-center justify-content-center">
                                <i class="fas fa-graduation-cap fa-2x text-white"></i>
                            </div>
                        @endif
                        <div>
                            <h1 class="mb-2">{{ $course->title }}</h1>
                            <p class="mb-2 opacity-90">{{ $course->short_description }}</p>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge badge-large bg-light text-dark">{{ $course->category->name }}</span>
                                <span class="badge badge-large bg-{{ $course->level === 'beginner' ? 'success' : ($course->level === 'intermediate' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($course->level) }}
                                </span>
                                <span class="badge badge-large bg-{{ $course->status === 'published' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($course->status) }}
                                </span>
                                @if($course->is_featured)
                                    <span class="badge badge-large bg-info">Featured</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex flex-column align-items-md-end">
                        @if($course->price > 0)
                            <div class="mb-2">
                                @if($course->discount_price)
                                    <span class="h4 me-2">${{ $course->discount_price }}</span>
                                    <span class="text-decoration-line-through opacity-75">${{ $course->price }}</span>
                                @else
                                    <span class="h4">${{ $course->price }}</span>
                                @endif
                            </div>
                        @else
                            <span class="h4 mb-2">Free</span>
                        @endif
                        <small class="opacity-75">
                            <i class="fas fa-clock me-1"></i>{{ $course->duration_hours }} hours
                            @if($course->max_students)
                                <i class="fas fa-users ms-3 me-1"></i>Max {{ $course->max_students }} students
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon bg-primary mx-auto">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['total_enrollments'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Total Enrollments</p>
                    <small class="text-success">{{ $stats['active_enrollments'] ?? 0 }} Active</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon bg-success mx-auto">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['completed_enrollments'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Completions</p>
                    @php
                        $completionRate = $stats['total_enrollments'] > 0 ? round(($stats['completed_enrollments'] / $stats['total_enrollments']) * 100, 1) : 0;
                    @endphp
                    <small class="text-success">{{ $completionRate }}% Rate</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon bg-warning mx-auto">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="mb-1">{{ number_format($stats['average_rating'] ?? 0, 1) }}</h3>
                    <p class="text-muted mb-0">Average Rating</p>
                    <small class="text-warning">{{ $stats['total_reviews'] ?? 0 }} Reviews</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-icon bg-info mx-auto">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['total_lessons'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Total Lessons</p>
                    <small class="text-info">{{ $stats['total_modules'] ?? 0 }} Modules</small>
                </div>
            </div>
        </div>

        <!-- Course Content and Details -->
        <div class="row">
            <!-- Course Content -->
            <div class="col-lg-8">
                <!-- Course Description -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Course Description
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $course->description }}</p>
                        
                        @if($course->learning_outcomes)
                            <h6 class="mt-4 mb-3">What You'll Learn</h6>
                            <ul class="list-unstyled">
                                @foreach($course->learning_outcomes as $outcome)
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>{{ $outcome }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if($course->requirements)
                            <h6 class="mt-4 mb-3">Requirements</h6>
                            <p class="text-muted">{{ $course->requirements }}</p>
                        @endif
                    </div>
                </div>

                <!-- Course Modules -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Course Content ({{ $course->modules->count() }} modules)
                        </h5>
                        <a href="{{ route('admin.courses.modules.index', $course) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-cog me-1"></i>Manage Content
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @if($course->modules->count() > 0)
                            @foreach($course->modules as $module)
                                <div class="module-card">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-folder me-2"></i>{{ $module->title }}
                                            </h6>
                                            <small class="text-muted">{{ $module->lessons->count() }} lessons</small>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        @if($module->lessons->count() > 0)
                                            @foreach($module->lessons as $lesson)
                                                <div class="lesson-item d-flex align-items-center">
                                                    <div class="lesson-icon bg-{{ $lesson->type === 'video' ? 'primary' : ($lesson->type === 'text' ? 'success' : ($lesson->type === 'quiz' ? 'warning' : 'info')) }}">
                                                        <i class="fas fa-{{ $lesson->type === 'video' ? 'play' : ($lesson->type === 'text' ? 'file-text' : ($lesson->type === 'quiz' ? 'question' : 'tasks')) }}"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $lesson->title }}</h6>
                                                        <small class="text-muted">{{ $lesson->duration_minutes }} minutes</small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-light text-dark">{{ ucfirst($lesson->type) }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="p-3 text-center text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <p class="mb-0">No lessons in this module</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No content yet</h5>
                                <p class="text-muted">Start by adding modules and lessons to this course.</p>
                                <a href="{{ route('admin.courses.modules.index', $course) }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Add Content
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Course Details Sidebar -->
            <div class="col-lg-4">
                <!-- Course Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>Course Details
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table info-table mb-0">
                            <tr>
                                <td><strong>Instructor</strong></td>
                                <td>
                                    @if($course->instructor)
                                        {{ $course->instructor->name }}
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Category</strong></td>
                                <td>{{ $course->category->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Level</strong></td>
                                <td>{{ ucfirst($course->level) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Duration</strong></td>
                                <td>{{ $course->duration_hours }} hours</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>
                                    <span class="badge bg-{{ $course->status === 'published' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Entry Test</strong></td>
                                <td>
                                    @if($course->requires_entry_test)
                                        <span class="badge bg-warning">Required ({{ $course->min_entry_test_score }}%)</span>
                                    @else
                                        <span class="badge bg-success">Not Required</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Certificate</strong></td>
                                <td>
                                    @if($course->has_certificate)
                                        <span class="badge bg-success">Provided</span>
                                    @else
                                        <span class="badge bg-secondary">Not Provided</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Created</strong></td>
                                <td>{{ $course->created_at->format('M d, Y') }}</td>
                            </tr>
                            @if($course->published_at)
                            <tr>
                                <td><strong>Published</strong></td>
                                <td>{{ $course->published_at->format('M d, Y') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-warning action-button">
                                <i class="fas fa-edit me-2"></i>Edit Course
                            </a>
                            <a href="{{ route('admin.courses.modules.index', $course) }}" class="btn btn-primary action-button">
                                <i class="fas fa-list me-2"></i>Manage Content
                            </a>
                            <a href="{{ route('admin.courses.enrollments.index', $course) }}" class="btn btn-info action-button">
                                <i class="fas fa-users me-2"></i>View Enrollments
                            </a>
                            
                            <hr>
                            
                            <form action="{{ route('admin.courses.duplicate', $course) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary action-button w-100">
                                    <i class="fas fa-copy me-2"></i>Duplicate Course
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.courses.toggle-status', $course) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-{{ $course->is_active ? 'warning' : 'success' }} action-button w-100">
                                    <i class="fas fa-toggle-{{ $course->is_active ? 'off' : 'on' }} me-2"></i>
                                    {{ $course->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            @if($course->enrollments->count() == 0)
                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger action-button w-100">
                                    <i class="fas fa-trash me-2"></i>Delete Course
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>