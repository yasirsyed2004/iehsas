{{-- File: resources/views/staff/dar/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAR - {{ $dar->report_date->format('M d, Y') }} - IEHSAS Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('staff.partials.styles')
</head>
<body>
    @include('staff.partials.sidebar', ['activeMenu' => 'dar'])

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>
                    <i class="fas fa-file-alt me-2"></i>Daily Activity Report
                    <span class="badge bg-{{ $dar->status_badge_class }}">{{ $dar->status_label }}</span>
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('staff.dar.index') }}">Daily Reports</a></li>
                        <li class="breadcrumb-item active">{{ $dar->report_date->format('M d, Y') }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                @if($dar->isEditable())
                    <a href="{{ route('staff.dar.edit', $dar) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <form action="{{ route('staff.dar.submit', $dar) }}" method="POST" onsubmit="return confirm('Submit this DAR? It will be locked for editing.')">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-1"></i>Submit DAR
                        </button>
                    </form>
                @endif
                <a href="{{ route('staff.dar.index') }}" class="btn btn-secondary">
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

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Report Info -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-day me-2"></i>{{ $dar->report_date->format('l, F d, Y') }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($dar->entries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 20%;">Time</th>
                                        <th>Activity</th>
                                        <th style="width: 20%;">Related Task</th>
                                        <th style="width: 15%;">Remarks</th>
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
                                                <a href="{{ route('staff.tasks.show', $entry->relatedTask) }}" class="text-decoration-none">
                                                    {{ Str::limit($entry->relatedTask->title, 30) }}
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
                            <p class="text-muted mb-0">No activity entries yet.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Addendums -->
                @if($dar->addendums->count() > 0 || $dar->canAddAddendum())
                <div class="card mt-4">
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

                        @if($dar->canAddAddendum())
                        <hr>
                        <form action="{{ route('staff.dar.add-addendum', $dar) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Add Addendum</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" name="content" rows="3" required placeholder="Add additional information or corrections..."></textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>Add Addendum
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar Info -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Report Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-{{ $dar->status_badge_class }}">{{ $dar->status_label }}</span>
                        </div>
                        @if($dar->department)
                        <div class="mb-3">
                            <small class="text-muted d-block">Department</small>
                            <strong>{{ $dar->department->name }}</strong>
                        </div>
                        @endif
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
                        <div class="mb-0">
                            <small class="text-muted d-block">Created</small>
                            <strong>{{ $dar->created_at->format('M d, Y h:i A') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Review Info -->
                @if($dar->reviewed_at || $dar->reviewer_comment)
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fas fa-clipboard-check me-2"></i>Manager Review</h6>
                    </div>
                    <div class="card-body">
                        @if($dar->reviewer)
                        <div class="mb-3">
                            <small class="text-muted d-block">Reviewed By</small>
                            <strong>{{ $dar->reviewer->name }}</strong>
                        </div>
                        @endif
                        @if($dar->reviewed_at)
                        <div class="mb-3">
                            <small class="text-muted d-block">Reviewed At</small>
                            <strong>{{ $dar->reviewed_at->format('M d, Y h:i A') }}</strong>
                        </div>
                        @endif
                        @if($dar->reviewer_comment)
                        <div class="mb-0">
                            <small class="text-muted d-block">Comment</small>
                            <p class="mb-0">{{ $dar->reviewer_comment }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
