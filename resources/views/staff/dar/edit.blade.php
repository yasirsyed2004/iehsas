{{-- File: resources/views/staff/dar/edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Daily Report - IEHSAS Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('staff.partials.styles')
</head>
<body>
    @include('staff.partials.sidebar', ['activeMenu' => 'dar'])

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-edit me-2"></i>Edit Daily Activity Report</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('staff.dar.index') }}">Daily Reports</a></li>
                        <li class="breadcrumb-item active">Edit - {{ $dar->report_date->format('M d, Y') }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('staff.dar.show', $dar) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($dar->status === 'revision_requested' && $dar->reviewer_comment)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-circle me-2"></i><strong>Revision Requested:</strong> {{ $dar->reviewer_comment }}
            </div>
        @endif

        <form action="{{ route('staff.dar.update', $dar) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-day me-2"></i>Report for: <strong>{{ $dar->report_date->format('l, M d, Y') }}</strong>
                        <span class="badge bg-{{ $dar->status_badge_class }} ms-2">{{ $dar->status_label }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div id="entriesContainer">
                        @foreach($dar->entries as $index => $entry)
                        <div class="entry-row border rounded p-3 mb-3" data-index="{{ $index }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0"><i class="fas fa-clock me-1"></i>Activity #{{ $index + 1 }}</h6>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-entry" {{ $dar->entries->count() <= 1 ? 'style=display:none' : '' }}>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Time From <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="entries[{{ $index }}][time_from]" required value="{{ old("entries.{$index}.time_from", substr($entry->time_from, 0, 5)) }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Time To <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="entries[{{ $index }}][time_to]" required value="{{ old("entries.{$index}.time_to", substr($entry->time_to, 0, 5)) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Related Task</label>
                                        <select class="form-select" name="entries[{{ $index }}][related_task_id]">
                                            <option value="">None</option>
                                            @foreach($tasks as $task)
                                                <option value="{{ $task->id }}" {{ old("entries.{$index}.related_task_id", $entry->related_task_id) == $task->id ? 'selected' : '' }}>
                                                    {{ Str::limit($task->title, 50) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Activity Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="entries[{{ $index }}][activity_description]" rows="2" required>{{ old("entries.{$index}.activity_description", $entry->activity_description) }}</textarea>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Remarks</label>
                                <input type="text" class="form-control" name="entries[{{ $index }}][remarks]" value="{{ old("entries.{$index}.remarks", $entry->remarks) }}">
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-outline-primary" id="addEntry">
                        <i class="fas fa-plus me-1"></i>Add Activity Entry
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('staff.dar.show', $dar) }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Save Changes
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let entryIndex = {{ $dar->entries->count() }};
        const tasksOptions = `<option value="">None</option>@foreach($tasks as $task)<option value="{{ $task->id }}">{{ Str::limit($task->title, 50) }}</option>@endforeach`;

        document.getElementById('addEntry').addEventListener('click', function() {
            const container = document.getElementById('entriesContainer');
            const html = `
                <div class="entry-row border rounded p-3 mb-3" data-index="${entryIndex}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0"><i class="fas fa-clock me-1"></i>Activity #${document.querySelectorAll('.entry-row').length + 1}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-entry"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><div class="mb-3"><label class="form-label">Time From <span class="text-danger">*</span></label><input type="time" class="form-control" name="entries[${entryIndex}][time_from]" required></div></div>
                        <div class="col-md-3"><div class="mb-3"><label class="form-label">Time To <span class="text-danger">*</span></label><input type="time" class="form-control" name="entries[${entryIndex}][time_to]" required></div></div>
                        <div class="col-md-6"><div class="mb-3"><label class="form-label">Related Task</label><select class="form-select" name="entries[${entryIndex}][related_task_id]">${tasksOptions}</select></div></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Activity Description <span class="text-danger">*</span></label><textarea class="form-control" name="entries[${entryIndex}][activity_description]" rows="2" required placeholder="Describe the activity performed..."></textarea></div>
                    <div class="mb-0"><label class="form-label">Remarks</label><input type="text" class="form-control" name="entries[${entryIndex}][remarks]" placeholder="Optional remarks..."></div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            entryIndex++;
            updateRemoveButtons();
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-entry')) {
                e.target.closest('.entry-row').remove();
                updateRemoveButtons();
                renumberEntries();
            }
        });

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.entry-row');
            rows.forEach(row => { row.querySelector('.remove-entry').style.display = rows.length > 1 ? 'inline-block' : 'none'; });
        }

        function renumberEntries() {
            document.querySelectorAll('.entry-row').forEach((row, idx) => {
                row.querySelector('h6').innerHTML = `<i class="fas fa-clock me-1"></i>Activity #${idx + 1}`;
            });
        }
    </script>
</body>
</html>
