{{-- File: resources/views/admin/tasks/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $task->title }} - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('admin.partials.styles')
    <style>
        .progress-lg { height: 25px; }
        .comment-item { border-left: 3px solid #007bff; }
        .share-link-box { background: #f8f9fa; border-radius: 8px; padding: 15px; }
    </style>
</head>
<body>
    @include('admin.partials.sidebar', ['activeMenu' => 'tasks'])

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-tasks me-2"></i>{{ Str::limit($task->title, 50) }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}">Tasks</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($task->title, 20) }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary">
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Task Details -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Task Details</h5>
                        <div>
                            <span class="badge bg-{{ $task->priority_badge_class }} me-2">
                                {{ ucfirst($task->priority) }} Priority
                            </span>
                            <span class="badge bg-{{ $task->status_badge_class }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>{{ $task->title }}</h4>
                        @if($task->description)
                            <p class="text-muted mt-3">{{ $task->description }}</p>
                        @endif

                        <!-- Progress Bar -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Progress</span>
                                <span class="fw-bold">{{ $task->progress_percentage }}%</span>
                            </div>
                            <div class="progress progress-lg">
                                <div class="progress-bar bg-{{ $task->progress_percentage >= 100 ? 'success' : ($task->progress_percentage >= 50 ? 'info' : 'warning') }}"
                                     style="width: {{ $task->progress_percentage }}%">
                                    {{ $task->progress_percentage }}%
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-building me-2"></i>Department:</strong>
                                    {{ $task->department?->name ?? 'Not assigned' }}
                                </p>
                                <p><strong><i class="fas fa-user me-2"></i>Assigned To:</strong>
                                    {{ $task->assignedUser?->name ?? 'Unassigned' }}
                                </p>
                                <p><strong><i class="fas fa-user-tie me-2"></i>Created By:</strong>
                                    {{ $task->assignedByAdmin?->name ?? 'System' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-calendar me-2"></i>Deadline:</strong>
                                    @if($task->deadline)
                                        <span class="{{ $task->is_overdue ? 'text-danger fw-bold' : '' }}">
                                            {{ $task->deadline->format('M d, Y') }}
                                            @if($task->is_overdue)
                                                (Overdue!)
                                            @elseif($task->days_until_deadline !== null && $task->days_until_deadline >= 0)
                                                ({{ $task->days_until_deadline }} days left)
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">No deadline</span>
                                    @endif
                                </p>
                                <p><strong><i class="fas fa-clock me-2"></i>Created:</strong>
                                    {{ $task->created_at->format('M d, Y H:i') }}
                                </p>
                                @if($task->completed_at)
                                <p><strong><i class="fas fa-check-circle me-2"></i>Completed:</strong>
                                    {{ $task->completed_at->format('M d, Y H:i') }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-comments me-2"></i>Comments & Updates</h5>
                    </div>
                    <div class="card-body">
                        <!-- Add Comment Form -->
                        <form action="{{ route('admin.tasks.add-comment', $task) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control" name="comment" rows="3" placeholder="Add a comment or update..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Add Comment
                            </button>
                        </form>

                        <hr>

                        <!-- Comments List -->
                        @if($task->comments->count() > 0)
                            @foreach($task->comments as $comment)
                            <div class="comment-item mb-3 p-3 bg-light rounded">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $comment->commenter_name }}</strong>
                                        <span class="badge bg-{{ $comment->commenter_type === 'admin' ? 'primary' : 'secondary' }} ms-2">
                                            {{ ucfirst($comment->commenter_type) }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <small class="text-muted me-2">{{ $comment->created_at->diffForHumans() }}</small>
                                        <form action="{{ route('admin.tasks.delete-comment', [$task, $comment]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('Delete this comment?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <p class="mb-0 mt-2">{{ $comment->comment }}</p>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No comments yet.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- External Sharing -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-share-alt me-2"></i>External Sharing</h5>
                    </div>
                    <div class="card-body">
                        @if($task->is_external_shared && $task->external_share_token)
                            <div class="share-link-box mb-3">
                                <small class="text-muted d-block mb-2">Share this link with external teams:</small>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="share-link"
                                           value="{{ route('task.public-view', $task->external_share_token) }}" readonly>
                                    <button class="btn btn-outline-primary btn-sm" onclick="copyShareLink()">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <form action="{{ route('admin.tasks.revoke-share', $task) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="fas fa-times me-1"></i>Revoke Share Link
                                </button>
                            </form>
                        @else
                            <p class="text-muted mb-3">Generate a public link to share task progress with external teams.</p>
                            <form action="{{ route('admin.tasks.generate-share', $task) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-link me-1"></i>Generate Share Link
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Attachments -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-paperclip me-2"></i>Attachments ({{ $task->attachments->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($task->attachments->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($task->attachments as $attachment)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas {{ $attachment->file_icon }} fa-lg me-2"></i>
                                        <div>
                                            <small class="d-block">{{ Str::limit($attachment->original_filename, 25) }}</small>
                                            <small class="text-muted">{{ $attachment->file_size_formatted }}</small>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ asset('storage/' . $attachment->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted text-center mb-0">No attachments</p>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-edit me-1"></i>Edit Task
                        </a>
                        <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-1"></i>Delete Task
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyShareLink() {
            const input = document.getElementById('share-link');
            input.select();
            document.execCommand('copy');
            alert('Link copied to clipboard!');
        }
    </script>
</body>
</html>
