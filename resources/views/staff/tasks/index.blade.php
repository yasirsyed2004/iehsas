{{-- File: resources/views/staff/tasks/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks - IEHSAS Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('staff.partials.styles')
    <style>
        .progress-sm { height: 6px; }
        .overdue { background-color: #fff5f5; }
    </style>
</head>
<body>
    @include('staff.partials.sidebar', ['activeMenu' => 'tasks'])

    <div class="main-content">
        <div class="mb-4">
            <h2><i class="fas fa-tasks me-2"></i>My Tasks</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Tasks</li>
                </ol>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center py-3">
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        <small>Total</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center py-3">
                        <h3 class="mb-0">{{ $stats['in_progress'] }}</h3>
                        <small>In Progress</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" style="background-color: #0d6efd;">
                    <div class="card-body text-center py-3 text-white">
                        <h3 class="mb-0">{{ $stats['submitted_for_review'] }}</h3>
                        <small>Under Review</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center py-3">
                        <h3 class="mb-0">{{ $stats['overdue'] }}</h3>
                        <small>Overdue</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('staff.tasks.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search tasks...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            @foreach(\App\Models\Task::getStatuses() as $key => $value)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="priority">
                            <option value="">All Priority</option>
                            @foreach(\App\Models\Task::getPriorities() as $key => $value)
                                <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Filter</button>
                        <a href="{{ route('staff.tasks.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i>Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tasks List -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Tasks ({{ $tasks->total() }})</h5>
            </div>
            <div class="card-body p-0">
                @if($tasks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Deadline</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                            <tr class="{{ $task->is_overdue ? 'overdue' : '' }}">
                                <td>
                                    <a href="{{ route('staff.tasks.show', $task) }}" class="text-decoration-none">
                                        <h6 class="mb-1">{{ Str::limit($task->title, 40) }}</h6>
                                    </a>
                                    <small class="text-muted">
                                        {{ $task->department?->name ?? '' }}
                                        @if($task->assignedByAdmin)
                                            | Assigned by {{ $task->assignedByAdmin->name }}
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $task->priority_badge_class }}">{{ ucfirst($task->priority) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $task->status_badge_class }}">
                                        {{ \App\Models\Task::getStatuses()[$task->status] ?? ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="progress progress-sm" style="width: 80px;">
                                        <div class="progress-bar bg-{{ $task->progress_percentage >= 100 ? 'success' : ($task->progress_percentage >= 50 ? 'info' : 'warning') }}"
                                             style="width: {{ $task->progress_percentage }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $task->progress_percentage }}%</small>
                                </td>
                                <td>
                                    @if($task->deadline)
                                        <span class="{{ $task->is_overdue ? 'text-danger fw-bold' : '' }}">
                                            {{ $task->deadline->format('M d, Y') }}
                                            @if($task->is_overdue)
                                                <i class="fas fa-exclamation-circle text-danger"></i>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('staff.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-tasks fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No tasks found</h4>
                    <p class="text-muted">You don't have any assigned tasks yet.</p>
                </div>
                @endif
            </div>
        </div>

        @if($tasks->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $tasks->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
