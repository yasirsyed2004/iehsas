<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Course Category - Admin LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    {{-- Include Shared Admin Styles --}}
    @include('admin.partials.styles')

    {{-- Page-specific styles --}}
    <style>
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            border-radius: 10px 10px 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd3 0%, #6a3f8e 100%);
        }
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
        <!-- Sidebar -->
    {{-- Include Shared Sidebar --}}
    @include('admin.partials.sidebar', ['activeMenu' => 'course-categories'])

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>
                    <i class="{{ $courseCategory->icon ?? 'fas fa-tags' }} me-2" style="color: {{ $courseCategory->color ?? '#6c757d' }}"></i>
                    {{ $courseCategory->name }}
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.course-categories.index') }}">Course Categories</a></li>
                        <li class="breadcrumb-item active">{{ $courseCategory->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.course-categories.edit', $courseCategory) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit Category
                </a>
                <a href="{{ route('admin.courses.create') }}?category_id={{ $courseCategory->id }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add Course
                </a>
            </div>
        </div>

        <!-- Category Details Card -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Category Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> {{ $courseCategory->name }}</p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-{{ $courseCategory->is_active ? 'success' : 'danger' }}">
                                        {{ $courseCategory->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                                <p><strong>Sort Order:</strong> {{ $courseCategory->sort_order }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Icon:</strong> <i class="{{ $courseCategory->icon ?? 'fas fa-book' }}"></i> {{ $courseCategory->icon ?? 'fas fa-book' }}</p>
                                <p><strong>Color:</strong> 
                                    <span class="badge" style="background-color: {{ $courseCategory->color ?? '#6c757d' }}">
                                        {{ $courseCategory->color ?? '#6c757d' }}
                                    </span>
                                </p>
                                <p><strong>Created:</strong> {{ $courseCategory->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        @if($courseCategory->description)
                            <hr>
                            <p><strong>Description:</strong></p>
                            <p>{{ $courseCategory->description }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Statistics Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Courses:</span>
                            <strong>{{ $courseCategory->courses->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Active Courses:</span>
                            <strong>{{ $courseCategory->courses->where('is_active', true)->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Draft Courses:</span>
                            <strong>{{ $courseCategory->courses->where('status', 'draft')->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Published Courses:</span>
                            <strong>{{ $courseCategory->courses->where('status', 'published')->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Total Enrollments:</span>
                            <strong>{{ $courseCategory->courses->sum(function($course) { return $course->enrollments->count(); }) }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.courses.create') }}?category_id={{ $courseCategory->id }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>Add New Course
                            </a>
                            <a href="{{ route('admin.course-categories.edit', $courseCategory) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit me-1"></i>Edit Category
                            </a>
                            <form action="{{ route('admin.course-categories.toggle-status', $courseCategory) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-{{ $courseCategory->is_active ? 'danger' : 'success' }} w-100">
                                    <i class="fas fa-toggle-{{ $courseCategory->is_active ? 'off' : 'on' }} me-1"></i>
                                    {{ $courseCategory->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses in this Category -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>Courses in this Category ({{ $courseCategory->courses->count() }})
                </h5>
            </div>
            <div class="card-body">
                @if($courseCategory->courses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Instructor</th>
                                    <th>Level</th>
                                    <th>Price</th>
                                    <th>Enrollments</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courseCategory->courses as $course)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.courses.show', $course) }}">
                                                {{ $course->title }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($course->instructor)
                                                {{ $course->instructor->name }}
                                            @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($course->level) }}</span>
                                        </td>
                                        <td>
                                            @if($course->discount_price)
                                                <del class="text-muted">${{ number_format($course->price, 2) }}</del>
                                                <span class="text-success">${{ number_format($course->discount_price, 2) }}</span>
                                            @else
                                                ${{ number_format($course->price, 2) }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $course->enrollments->count() }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'draft' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.courses.modules.index', $course) }}" class="btn btn-success" title="Modules">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-graduation-cap fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No courses in this category yet</h5>
                        <p class="text-muted">Create your first course in this category to get started.</p>
                        <a href="{{ route('admin.courses.create') }}?category_id={{ $courseCategory->id }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Create Course
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>