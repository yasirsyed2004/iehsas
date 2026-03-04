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
        .audit-timeline { position: relative; padding-left: 30px; }
        .audit-timeline::before { content: ''; position: absolute; left: 14px; top: 0; bottom: 0; width: 2px; background: #dee2e6; }
        .audit-item { position: relative; margin-bottom: 15px; }
        .audit-item::before { content: ''; position: absolute; left: -22px; top: 5px; width: 10px; height: 10px; border-radius: 50%; background: #6c757d; border: 2px solid #fff; }
        .progress-log-card { border-left: 3px solid #17a2b8; }
        .progress-log-card.locked { border-left-color: #28a745; }
        .review-item { border-left: 3px solid; }
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
                @if($task->isEditable())
                <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                @endif
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

        {{-- Locked Banner --}}
        @if($task->is_locked)
            <div class="alert alert-dark d-flex align-items-center">
                <i class="fas fa-lock fa-lg me-3"></i>
                <div>
                    <strong>This task is locked.</strong>
                    @if($task->closedByAdmin)
                        Closed by {{ $task->closedByAdmin->name }} on {{ $task->closed_at->format('M d, Y H:i') }}.
                    @endif
                </div>
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
                                {{ \App\Models\Task::getStatuses()[$task->status] ?? ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                            @if($task->is_overdue)
                                <span class="badge bg-danger ms-1">
                                    <i class="fas fa-exclamation-circle me-1"></i>Overdue
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>{{ $task->title }}</h4>
                        @if($task->description)
                            <p class="text-muted mt-3">{!! nl2br(e($task->description)) !!}</p>
                        @endif

                        @if($task->expected_deliverables)
                            <div class="mt-3 p-3 bg-light rounded">
                                <h6 class="mb-2"><i class="fas fa-bullseye me-2"></i>Expected Deliverables</h6>
                                <p class="mb-0 text-muted">{!! nl2br(e($task->expected_deliverables)) !!}</p>
                            </div>
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
                                    @if($task->assignedUser?->email)
                                        <small class="text-muted">({{ $task->assignedUser->email }})</small>
                                    @endif
                                </p>
                                <p><strong><i class="fas fa-user-tie me-2"></i>Created By:</strong>
                                    {{ $task->assignedByAdmin?->name ?? 'System' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                @if($task->start_date)
                                <p><strong><i class="fas fa-play me-2"></i>Start Date:</strong>
                                    {{ $task->start_date->format('M d, Y') }}
                                </p>
                                @endif
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
                                <p><strong><i class="fas fa-check-circle me-2 text-success"></i>Completed:</strong>
                                    {{ $task->completed_at->format('M d, Y H:i') }}
                                </p>
                                @endif
                                @if($task->closed_at)
                                <p><strong><i class="fas fa-lock me-2 text-dark"></i>Closed:</strong>
                                    {{ $task->closed_at->format('M d, Y H:i') }}
                                    @if($task->closedByAdmin)
                                        by {{ $task->closedByAdmin->name }}
                                    @endif
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Manager Review Section --}}
                @if($task->canBeReviewedByManager() && !$task->is_locked)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="fas fa-clipboard-check me-2"></i>Manager Review</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.tasks.submit-review', $task) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="action" class="form-label">Review Action <span class="text-danger">*</span></label>
                                    <select class="form-select @error('action') is-invalid @enderror" id="action" name="action" required>
                                        <option value="">Select Action</option>
                                        <option value="remark">Add Remark</option>
                                        <option value="revision_requested">Request Revision</option>
                                        <option value="approved">Approve Task</option>
                                        <option value="closed">Close Task</option>
                                    </select>
                                    @error('action')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label for="remarks" class="form-label">Remarks <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('remarks') is-invalid @enderror"
                                              id="remarks" name="remarks" rows="2" placeholder="Enter your review remarks..." required></textarea>
                                    @error('remarks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to submit this review?')">
                                <i class="fas fa-paper-plane me-1"></i>Submit Review
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                {{-- Review History --}}
                @if($task->reviews->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Review History ({{ $task->reviews->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @foreach($task->reviews as $review)
                        <div class="review-item mb-3 p-3 bg-light rounded" style="border-left-color: var(--bs-{{ $review->action_badge_class }});">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <i class="fas {{ $review->action_icon }} me-2"></i>
                                    <span class="badge bg-{{ $review->action_badge_class }}">{{ $review->action_label }}</span>
                                    <strong class="ms-2">{{ $review->admin?->name ?? 'Admin' }}</strong>
                                </div>
                                <small class="text-muted">{{ $review->created_at->format('M d, Y H:i') }}</small>
                            </div>
                            <p class="mb-0 mt-2">{{ $review->remarks }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Daily Progress Logs --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2"></i>Daily Progress Logs ({{ $task->progressLogs->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($task->progressLogs->count() > 0)
                            @foreach($task->progressLogs as $log)
                            <div class="progress-log-card card mb-3 {{ $log->is_locked ? 'locked' : '' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="fas fa-calendar-day me-2"></i>{{ $log->log_date->format('M d, Y') }}
                                                <span class="badge bg-{{ $log->progress_percentage >= 100 ? 'success' : ($log->progress_percentage >= 50 ? 'info' : 'warning') }} ms-2">
                                                    {{ $log->progress_percentage }}%
                                                </span>
                                                @if($log->is_locked)
                                                    <span class="badge bg-success ms-1"><i class="fas fa-check me-1"></i>Submitted</span>
                                                @else
                                                    <span class="badge bg-warning ms-1"><i class="fas fa-edit me-1"></i>Draft</span>
                                                @endif
                                            </h6>
                                            <small class="text-muted">
                                                By {{ $log->user?->name ?? 'Unknown' }}
                                                @if($log->submitted_at)
                                                    | Submitted: {{ $log->submitted_at->format('M d, Y H:i') }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    @if($log->description)
                                    <p class="mt-2 mb-0">{!! nl2br(e($log->description)) !!}</p>
                                    @endif

                                    @if($log->attachments->count() > 0)
                                    <div class="mt-2">
                                        <small class="text-muted fw-bold">Attachments:</small>
                                        <div class="d-flex flex-wrap gap-2 mt-1">
                                            @foreach($log->attachments as $attachment)
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="fas {{ $attachment->file_icon }} me-1"></i>
                                                {{ Str::limit($attachment->original_filename, 20) }}
                                                <small>({{ $attachment->file_size_formatted }})</small>
                                            </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center mb-0">No progress logs submitted yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-comments me-2"></i>Comments & Updates</h5>
                    </div>
                    <div class="card-body">
                        <!-- Add Comment Form -->
                        @if(!$task->is_locked)
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
                        @endif

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
                                        @if(!$task->is_locked)
                                        <form action="{{ route('admin.tasks.delete-comment', [$task, $comment]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('Delete this comment?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
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
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        @if($task->isEditable())
                        <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-edit me-1"></i>Edit Task
                        </a>
                        @endif
                        <a href="{{ route('admin.tasks.detail-pdf', $task) }}" class="btn btn-outline-danger w-100 mb-2">
                            <i class="fas fa-file-pdf me-1"></i>Export PDF
                        </a>
                        <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-1"></i>Delete Task
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Audit Trail --}}
                @if($task->auditLogs->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-clipboard-list me-2"></i>Audit Trail</h5>
                    </div>
                    <div class="card-body">
                        <div class="audit-timeline">
                            @foreach($task->auditLogs->take(20) as $log)
                            <div class="audit-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <i class="fas {{ $log->action_icon }} me-1"></i>
                                        <small class="fw-bold">{{ $log->performer_name }}</small>
                                    </div>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </div>
                                @if($log->description)
                                    <small class="text-muted d-block">{{ $log->description }}</small>
                                @else
                                    <small class="text-muted d-block">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                        @if($log->field_name)
                                            : {{ str_replace('_', ' ', $log->field_name) }}
                                            @if($log->old_value && $log->new_value)
                                                from "{{ Str::limit($log->old_value, 30) }}" to "{{ Str::limit($log->new_value, 30) }}"
                                            @endif
                                        @endif
                                    </small>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @if($task->auditLogs->count() > 20)
                            <div class="text-center mt-2">
                                <small class="text-muted">Showing latest 20 of {{ $task->auditLogs->count() }} entries</small>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
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
