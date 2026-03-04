{{-- File: resources/views/staff/tasks/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $task->title }} - IEHSAS Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('staff.partials.styles')
    <style>
        .progress-lg { height: 25px; }
        .comment-item { border-left: 3px solid #0ea5e9; }
        .progress-log-card { border-left: 3px solid #17a2b8; }
        .progress-log-card.locked { border-left-color: #28a745; }
        .review-item { border-left: 3px solid; }
    </style>
</head>
<body>
    @include('staff.partials.sidebar', ['activeMenu' => 'tasks'])

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-tasks me-2"></i>{{ Str::limit($task->title, 50) }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('staff.tasks.index') }}">My Tasks</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($task->title, 20) }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('staff.tasks.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
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
            <div class="col-md-8">
                <!-- Task Details -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Task Details</h5>
                        <div>
                            <span class="badge bg-{{ $task->priority_badge_class }} me-2">{{ ucfirst($task->priority) }} Priority</span>
                            <span class="badge bg-{{ $task->status_badge_class }}">
                                {{ \App\Models\Task::getStatuses()[$task->status] ?? ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                            @if($task->is_overdue)
                                <span class="badge bg-danger ms-1"><i class="fas fa-exclamation-circle me-1"></i>Overdue</span>
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
                                <p><strong><i class="fas fa-building me-2"></i>Department:</strong> {{ $task->department?->name ?? '-' }}</p>
                                <p><strong><i class="fas fa-user-tie me-2"></i>Assigned By:</strong> {{ $task->assignedByAdmin?->name ?? 'System' }}</p>
                            </div>
                            <div class="col-md-6">
                                @if($task->start_date)
                                <p><strong><i class="fas fa-play me-2"></i>Start Date:</strong> {{ $task->start_date->format('M d, Y') }}</p>
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
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Daily Progress Form --}}
                @if($task->isEditableByEmployee())
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>Submit Today's Progress ({{ now()->format('M d, Y') }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($todayLog && $todayLog->is_locked)
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                You've already submitted today's progress ({{ $todayLog->progress_percentage }}%).
                                You can submit again tomorrow.
                            </div>
                        @else
                            <form action="{{ route('staff.tasks.submit-progress', $task) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="progress_percentage" class="form-label">Progress (%) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('progress_percentage') is-invalid @enderror"
                                               id="progress_percentage" name="progress_percentage"
                                               value="{{ old('progress_percentage', $todayLog->progress_percentage ?? $task->progress_percentage) }}"
                                               min="0" max="100" required>
                                        @error('progress_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label for="attachments" class="form-label">Attachments</label>
                                        <input type="file" class="form-control @error('attachments.*') is-invalid @enderror"
                                               id="attachments" name="attachments[]" multiple>
                                        <small class="text-muted">Max 10MB per file.</small>
                                        @error('attachments.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="description" class="form-label">Work Done Today <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                  id="description" name="description" rows="4"
                                                  placeholder="Describe what you worked on today..."
                                                  required>{{ old('description', $todayLog->description ?? '') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="alert alert-warning py-2 mb-3">
                                    <small><i class="fas fa-exclamation-triangle me-1"></i> Once submitted, today's progress entry will be locked and cannot be edited.</small>
                                </div>
                                <button type="submit" class="btn btn-primary" onclick="return confirm('Submit and lock today\'s progress? This cannot be undone.')">
                                    <i class="fas fa-paper-plane me-1"></i>Submit Daily Progress
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Submit for Review --}}
                @if($task->canSubmitForReview())
                <div class="card mb-4 border-primary">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><i class="fas fa-clipboard-check me-2"></i>Ready for Review?</h6>
                            <small class="text-muted">Submit this task for manager review when you believe it's complete.</small>
                        </div>
                        <form action="{{ route('staff.tasks.submit-for-review', $task) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Submit this task for manager review?')">
                                <i class="fas fa-paper-plane me-1"></i>Submit for Review
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                {{-- Manager Reviews --}}
                @if($task->reviews->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Manager Reviews ({{ $task->reviews->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @foreach($task->reviews as $review)
                        <div class="review-item mb-3 p-3 bg-light rounded" style="border-left-color: var(--bs-{{ $review->action_badge_class }});">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <i class="fas {{ $review->action_icon }} me-2"></i>
                                    <span class="badge bg-{{ $review->action_badge_class }}">{{ $review->action_label }}</span>
                                    <strong class="ms-2">{{ $review->admin?->name ?? 'Manager' }}</strong>
                                </div>
                                <small class="text-muted">{{ $review->created_at->format('M d, Y H:i') }}</small>
                            </div>
                            <p class="mb-0 mt-2">{{ $review->remarks }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Progress History --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2"></i>Progress History ({{ $task->progressLogs->count() }})</h5>
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
                                                @endif
                                            </h6>
                                            @if($log->submitted_at)
                                                <small class="text-muted">Submitted: {{ $log->submitted_at->format('M d, Y H:i') }}</small>
                                            @endif
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

                <!-- Comments -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-comments me-2"></i>Comments</h5>
                    </div>
                    <div class="card-body">
                        @if(!$task->is_locked)
                        <form action="{{ route('staff.tasks.add-comment', $task) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control" name="comment" rows="3" placeholder="Add a comment..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Add Comment
                            </button>
                        </form>
                        <hr>
                        @endif

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
                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
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
                <!-- Attachments -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-paperclip me-2"></i>Task Attachments ({{ $task->attachments->count() }})</h5>
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
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted text-center mb-0">No attachments</p>
                        @endif
                    </div>
                </div>

                <!-- Task Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-info me-2"></i>Task Info</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Created:</strong> {{ $task->created_at->format('M d, Y') }}</p>
                        @if($task->completed_at)
                        <p class="mb-2"><strong>Completed:</strong> {{ $task->completed_at->format('M d, Y') }}</p>
                        @endif
                        @if($task->submitted_for_review_at)
                        <p class="mb-2"><strong>Submitted for Review:</strong> {{ $task->submitted_for_review_at->format('M d, Y') }}</p>
                        @endif
                        @if($task->closed_at)
                        <p class="mb-2"><strong>Closed:</strong> {{ $task->closed_at->format('M d, Y') }}</p>
                        @endif
                        <p class="mb-0"><strong>Total Progress Entries:</strong> {{ $task->progressLogs->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
