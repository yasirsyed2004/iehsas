{{-- File: resources/views/admin/dar/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAR - {{ $dar->user->name ?? 'N/A' }} - {{ $dar->report_date->format('M d, Y') }} - Admin IEHSAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('admin.partials.styles')
</head>
<body>
    @include('admin.partials.sidebar', ['activeMenu' => 'dar'])

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>
                    <i class="fas fa-file-alt me-2"></i>Daily Activity Report
                    <span class="badge bg-{{ $dar->status_badge_class }}">{{ $dar->status_label }}</span>
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.dar.index') }}">Daily Reports</a></li>
                        <li class="breadcrumb-item active">{{ $dar->user->name ?? 'N/A' }} - {{ $dar->report_date->format('M d, Y') }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.dar.detail-pdf', $dar) }}" class="btn btn-outline-danger">
                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                </a>
                <a href="{{ route('admin.dar.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
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
            <div class="col-md-8">
                <!-- Activity Entries -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-day me-2"></i>{{ $dar->report_date->format('l, F d, Y') }}
                            - {{ $dar->user->name ?? 'N/A' }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($dar->entries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 18%;">Time</th>
                                        <th>Activity Description</th>
                                        <th style="width: 18%;">Related Task</th>
                                        <th style="width: 12%;">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dar->entries as $index => $entry)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <small>
                                                {{ date('h:i A', strtotime($entry->time_from)) }}<br>
                                                <span class="text-muted">to</span><br>
                                                {{ date('h:i A', strtotime($entry->time_to)) }}
                                            </small>
                                        </td>
                                        <td>{{ $entry->activity_description }}</td>
                                        <td>
                                            @if($entry->relatedTask)
                                                <a href="{{ route('admin.tasks.show', $entry->relatedTask) }}" class="text-decoration-none">
                                                    {{ Str::limit($entry->relatedTask->title, 25) }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td><small class="text-muted">{{ $entry->remarks ?? '-' }}</small></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No activity entries.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Addendums -->
                @if($dar->addendums->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-paperclip me-2"></i>Addendums</h5>
                    </div>
                    <div class="card-body">
                        @foreach($dar->addendums as $addendum)
                        <div class="border-start border-3 border-info ps-3 mb-3">
                            <p class="mb-1">{{ $addendum->content }}</p>
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>{{ $addendum->addedBy->name ?? 'Unknown' }}
                                <i class="fas fa-clock ms-2 me-1"></i>{{ $addendum->created_at->format('M d, Y h:i A') }}
                            </small>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Review Form -->
                @if($dar->canBeReviewed())
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="fas fa-clipboard-check me-2"></i>Review this DAR</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.dar.review', $dar) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Action <span class="text-danger">*</span></label>
                                <select name="action" class="form-select @error('action') is-invalid @enderror" required>
                                    <option value="">Select Action</option>
                                    <option value="approved">Approve</option>
                                    <option value="revision_requested">Request Revision</option>
                                </select>
                                @error('action')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Comment</label>
                                <textarea name="reviewer_comment" class="form-control @error('reviewer_comment') is-invalid @enderror" rows="3" placeholder="Optional feedback..."></textarea>
                                @error('reviewer_comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-check me-1"></i>Submit Review
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fas fa-user me-2"></i>Employee Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Name</small>
                            <strong>{{ $dar->user->name ?? 'N/A' }}</strong>
                        </div>
                        @if($dar->department)
                        <div class="mb-3">
                            <small class="text-muted d-block">Department</small>
                            <strong>{{ $dar->department->name }}</strong>
                        </div>
                        @endif
                        @if($dar->reportingManager)
                        <div class="mb-0">
                            <small class="text-muted d-block">Reporting Manager</small>
                            <strong>{{ $dar->reportingManager->name }}</strong>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Report Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-{{ $dar->status_badge_class }}">{{ $dar->status_label }}</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Report Date</small>
                            <strong>{{ $dar->report_date->format('l, M d, Y') }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Activities</small>
                            <strong>{{ $dar->entries->count() }} entries</strong>
                        </div>
                        @if($dar->submitted_at)
                        <div class="mb-3">
                            <small class="text-muted d-block">Submitted At</small>
                            <strong>{{ $dar->submitted_at->format('M d, Y h:i A') }}</strong>
                        </div>
                        @endif
                        @if($dar->reviewed_at)
                        <div class="mb-3">
                            <small class="text-muted d-block">Reviewed At</small>
                            <strong>{{ $dar->reviewed_at->format('M d, Y h:i A') }}</strong>
                        </div>
                        @endif
                        @if($dar->reviewer)
                        <div class="mb-3">
                            <small class="text-muted d-block">Reviewed By</small>
                            <strong>{{ $dar->reviewer->name }}</strong>
                        </div>
                        @endif
                        @if($dar->reviewer_comment)
                        <div class="mb-0">
                            <small class="text-muted d-block">Reviewer Comment</small>
                            <p class="mb-0">{{ $dar->reviewer_comment }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
