{{-- File: resources/views/staff/dar/create.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Daily Report - IEHSAS Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('staff.partials.styles')
</head>
<body>
    @include('staff.partials.sidebar', ['activeMenu' => 'dar'])

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-plus-circle me-2"></i>Create Daily Activity Report</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('staff.dar.index') }}">Daily Reports</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('staff.dar.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>Please fix the errors below.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('staff.dar.store') }}" method="POST" id="darForm">
            @csrf

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-day me-2"></i>Report for: <strong>{{ \Carbon\Carbon::parse($today)->format('l, M d, Y') }}</strong>
                    </h5>
                </div>
                <div class="card-body">
                    <div id="entriesContainer">
                        <!-- Entry Row Template -->
                        <div class="entry-row border rounded p-3 mb-3" data-index="0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0"><i class="fas fa-clock me-1"></i>Activity #1</h6>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-entry" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Time From <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="entries[0][time_from]" required value="{{ old('entries.0.time_from', '09:00') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Time To <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="entries[0][time_to]" required value="{{ old('entries.0.time_to', '10:00') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Related Task</label>
                                        <select class="form-select" name="entries[0][related_task_id]">
                                            <option value="">None</option>
                                            @foreach($tasks as $task)
                                                <option value="{{ $task->id }}" {{ old('entries.0.related_task_id') == $task->id ? 'selected' : '' }}>
                                                    {{ Str::limit($task->title, 50) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Activity Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="entries[0][activity_description]" rows="2" required placeholder="Describe the activity performed...">{{ old('entries.0.activity_description') }}</textarea>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Remarks</label>
                                <input type="text" class="form-control" name="entries[0][remarks]" placeholder="Optional remarks..." value="{{ old('entries.0.remarks') }}">
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary" id="addEntry">
                        <i class="fas fa-plus me-1"></i>Add Activity Entry
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('staff.dar.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Save as Draft
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let entryIndex = 1;
        const tasksOptions = `<option value="">None</option>@foreach($tasks as $task)<option value="{{ $task->id }}">{{ Str::limit($task->title, 50) }}</option>@endforeach`;

        document.getElementById('addEntry').addEventListener('click', function() {
            const container = document.getElementById('entriesContainer');
            const html = `
                <div class="entry-row border rounded p-3 mb-3" data-index="${entryIndex}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0"><i class="fas fa-clock me-1"></i>Activity #${entryIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-entry">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Time From <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="entries[${entryIndex}][time_from]" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Time To <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="entries[${entryIndex}][time_to]" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Related Task</label>
                                <select class="form-select" name="entries[${entryIndex}][related_task_id]">${tasksOptions}</select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Activity Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="entries[${entryIndex}][activity_description]" rows="2" required placeholder="Describe the activity performed..."></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Remarks</label>
                        <input type="text" class="form-control" name="entries[${entryIndex}][remarks]" placeholder="Optional remarks...">
                    </div>
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
            rows.forEach(row => {
                const btn = row.querySelector('.remove-entry');
                btn.style.display = rows.length > 1 ? 'inline-block' : 'none';
            });
        }

        function renumberEntries() {
            const rows = document.querySelectorAll('.entry-row');
            rows.forEach((row, idx) => {
                row.querySelector('h6').innerHTML = `<i class="fas fa-clock me-1"></i>Activity #${idx + 1}`;
            });
        }
    </script>
</body>
</html>
