{{-- File: resources/views/staff/dashboard/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - IEHSAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('staff.partials.styles')
</head>
<body>
    @include('staff.partials.sidebar', ['activeMenu' => 'dashboard'])

    <div class="main-content">
        <div class="mb-4">
            <h2><i class="fas fa-tachometer-alt me-2"></i>Welcome, {{ Auth::guard('web')->user()->name }}!</h2>
            <p class="text-muted">Here's an overview of your tasks and activities.</p>
        </div>

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center py-3">
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        <small>Total Tasks</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center py-3">
                        <h3 class="mb-0">{{ $stats['not_started'] }}</h3>
                        <small>Not Started</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-info text-white">
                    <div class="card-body text-center py-3">
                        <h3 class="mb-0">{{ $stats['in_progress'] }}</h3>
                        <small>In Progress</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card" style="background-color: #0d6efd;">
                    <div class="card-body text-center py-3 text-white">
                        <h3 class="mb-0">{{ $stats['submitted_for_review'] }}</h3>
                        <small>Under Review</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-success text-white">
                    <div class="card-body text-center py-3">
                        <h3 class="mb-0">{{ $stats['completed'] }}</h3>
                        <small>Completed</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center py-3">
                        <h3 class="mb-0">{{ $stats['overdue'] }}</h3>
                        <small>Overdue</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Tasks -->
            <div class="col-md-7">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-clock me-2"></i>Recent Tasks</h5>
                    </div>
                    <div class="card-body p-0">
                        @if($recentTasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Task</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTasks as $task)
                                    <tr>
                                        <td>
                                            <strong>{{ Str::limit($task->title, 30) }}</strong>
                                            @if($task->department)
                                                <br><small class="text-muted">{{ $task->department->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $task->status_badge_class }}">
                                                {{ \App\Models\Task::getStatuses()[$task->status] ?? ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress" style="width: 80px; height: 6px;">
                                                <div class="progress-bar bg-{{ $task->progress_percentage >= 100 ? 'success' : 'info' }}"
                                                     style="width: {{ $task->progress_percentage }}%"></div>
                                            </div>
                                            <small>{{ $task->progress_percentage }}%</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('staff.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
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
                            <p class="text-muted mb-0">No tasks assigned yet.</p>
                        </div>
                        @endif
                    </div>
                    @if($recentTasks->count() > 0)
                    <div class="card-footer text-center">
                        <a href="{{ route('staff.tasks.index') }}" class="btn btn-sm btn-primary">View All Tasks</a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Deadlines & Notifications -->
            <div class="col-md-5">
                <!-- Upcoming Deadlines -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-calendar-alt me-2 text-danger"></i>Upcoming Deadlines (7 days)</h5>
                    </div>
                    <div class="card-body">
                        @if($upcomingDeadlines->count() > 0)
                            @foreach($upcomingDeadlines as $task)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <a href="{{ route('staff.tasks.show', $task) }}" class="text-decoration-none">
                                        <strong>{{ Str::limit($task->title, 25) }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        <span class="badge bg-{{ $task->priority_badge_class }}">{{ ucfirst($task->priority) }}</span>
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="{{ $task->days_until_deadline <= 2 ? 'text-danger fw-bold' : 'text-muted' }}">
                                        {{ $task->deadline->format('M d') }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $task->days_until_deadline }} days</small>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center mb-0">No upcoming deadlines this week.</p>
                        @endif
                    </div>
                </div>

                <!-- Quick Notifications -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-bell me-2"></i>Notifications</h5>
                        @if($unreadNotifications > 0)
                            <span class="badge bg-danger">{{ $unreadNotifications }} new</span>
                        @endif
                    </div>
                    <div class="card-body text-center">
                        <a href="{{ route('staff.notifications.index') }}" class="btn btn-outline-primary btn-sm">
                            View All Notifications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
