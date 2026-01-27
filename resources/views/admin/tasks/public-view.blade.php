{{-- File: resources/views/admin/tasks/public-view.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $task->title }} - Task Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .task-card {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .task-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
        }
        .progress-lg { height: 30px; border-radius: 15px; }
        .progress-bar { border-radius: 15px; }
        .status-badge {
            font-size: 1rem;
            padding: 10px 20px;
            border-radius: 25px;
        }
        .info-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .comment-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="task-card bg-white">
                    <!-- Header -->
                    <div class="task-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h2 class="mb-2">{{ $task->title }}</h2>
                                @if($task->department)
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-building me-1"></i>{{ $task->department->name }}
                                    </span>
                                @endif
                            </div>
                            <span class="status-badge bg-{{ $task->status_badge_class }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-4">
                        <!-- Progress -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="mb-0">Progress</h5>
                                <h5 class="mb-0 text-primary">{{ $task->progress_percentage }}%</h5>
                            </div>
                            <div class="progress progress-lg">
                                <div class="progress-bar bg-{{ $task->progress_percentage >= 100 ? 'success' : ($task->progress_percentage >= 50 ? 'info' : 'warning') }}"
                                     style="width: {{ $task->progress_percentage }}%">
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($task->description)
                        <div class="mb-4">
                            <h5><i class="fas fa-align-left me-2"></i>Description</h5>
                            <p class="text-muted">{{ $task->description }}</p>
                        </div>
                        @endif

                        <!-- Info -->
                        <div class="card mb-4">
                            <div class="card-body p-0">
                                <div class="info-item d-flex justify-content-between">
                                    <span><i class="fas fa-flag me-2 text-{{ $task->priority_badge_class }}"></i>Priority</span>
                                    <span class="badge bg-{{ $task->priority_badge_class }}">{{ ucfirst($task->priority) }}</span>
                                </div>
                                <div class="info-item d-flex justify-content-between">
                                    <span><i class="fas fa-calendar me-2 text-primary"></i>Deadline</span>
                                    <span class="{{ $task->is_overdue ? 'text-danger fw-bold' : '' }}">
                                        @if($task->deadline)
                                            {{ $task->deadline->format('M d, Y') }}
                                            @if($task->is_overdue)
                                                <span class="badge bg-danger ms-2">Overdue</span>
                                            @endif
                                        @else
                                            No deadline
                                        @endif
                                    </span>
                                </div>
                                <div class="info-item d-flex justify-content-between">
                                    <span><i class="fas fa-clock me-2 text-secondary"></i>Created</span>
                                    <span>{{ $task->created_at->format('M d, Y') }}</span>
                                </div>
                                @if($task->completed_at)
                                <div class="info-item d-flex justify-content-between">
                                    <span><i class="fas fa-check-circle me-2 text-success"></i>Completed</span>
                                    <span class="text-success">{{ $task->completed_at->format('M d, Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Comments/Updates -->
                        @if($task->comments->count() > 0)
                        <div class="mb-4">
                            <h5><i class="fas fa-comments me-2"></i>Updates ({{ $task->comments->count() }})</h5>
                            @foreach($task->comments->take(5) as $comment)
                            <div class="comment-item">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>{{ $comment->commenter_name }}</strong>
                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0">{{ $comment->comment }}</p>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Attachments -->
                        @if($task->attachments->count() > 0)
                        <div>
                            <h5><i class="fas fa-paperclip me-2"></i>Attachments ({{ $task->attachments->count() }})</h5>
                            <div class="list-group">
                                @foreach($task->attachments as $attachment)
                                <a href="{{ asset('storage/' . $attachment->file_path) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank">
                                    <span>
                                        <i class="fas {{ $attachment->file_icon }} me-2"></i>
                                        {{ $attachment->original_filename }}
                                    </span>
                                    <span class="badge bg-secondary">{{ $attachment->file_size_formatted }}</span>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="text-center py-3 bg-light">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            This is a read-only view shared externally
                        </small>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-white opacity-75">
                        Last updated: {{ $task->updated_at->format('M d, Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
