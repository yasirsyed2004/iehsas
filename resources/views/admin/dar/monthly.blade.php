{{-- File: resources/views/admin/dar/monthly.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly DAR Report - Admin IEHSAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('admin.partials.styles')
    <style>
        .calendar-cell {
            width: 28px;
            height: 28px;
            text-align: center;
            font-size: 11px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .status-submitted { background-color: #0dcaf0; color: white; }
        .status-reviewed { background-color: #198754; color: white; }
        .status-revision_requested { background-color: #ffc107; color: #000; }
        .status-draft { background-color: #6c757d; color: white; }
        .status-missing { background-color: #dc3545; color: white; }
        .status-weekend { background-color: #e9ecef; color: #6c757d; }
        .status-future { background-color: #f8f9fa; color: #adb5bd; }
        .monthly-table th { font-size: 12px; padding: 6px 4px; text-align: center; }
        .monthly-table td { padding: 4px; text-align: center; vertical-align: middle; }
        .compliance-bar { height: 6px; border-radius: 3px; }
    </style>
</head>
<body>
    @include('admin.partials.sidebar', ['activeMenu' => 'dar'])

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-calendar-alt me-2"></i>Monthly DAR Compliance</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.dar.index') }}">Daily Reports</a></li>
                        <li class="breadcrumb-item active">Monthly Report</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.dar.monthly-pdf', request()->query()) }}" class="btn btn-outline-danger">
                    <i class="fas fa-file-pdf me-1"></i>PDF
                </a>
                <a href="{{ route('admin.dar.monthly-excel', request()->query()) }}" class="btn btn-outline-success">
                    <i class="fas fa-file-excel me-1"></i>Excel
                </a>
                <a href="{{ route('admin.dar.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to DARs
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.dar.monthly') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select">
                            @for($y = now()->year; $y >= now()->year - 2; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-select">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Generate Report</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Legend -->
        <div class="card mb-4">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <small class="fw-bold">Legend:</small>
                    <small><span class="calendar-cell status-reviewed">1</span> Reviewed</small>
                    <small><span class="calendar-cell status-submitted">1</span> Submitted</small>
                    <small><span class="calendar-cell status-revision_requested">1</span> Revision</small>
                    <small><span class="calendar-cell status-draft">1</span> Draft</small>
                    <small><span class="calendar-cell status-missing">1</span> Missing</small>
                    <small><span class="calendar-cell status-weekend">1</span> Weekend</small>
                    <small><span class="calendar-cell status-future">1</span> Future</small>
                </div>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>{{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }} - Staff DAR Compliance
                </h5>
            </div>
            <div class="card-body p-0">
                @if(count($calendarData) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 monthly-table">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 150px; text-align: left;">Employee</th>
                                @for($day = 1; $day <= $daysInMonth; $day++)
                                    @php $date = \Carbon\Carbon::create($year, $month, $day); @endphp
                                    <th class="{{ $date->isWeekend() ? 'bg-light' : '' }}">
                                        {{ $day }}<br>
                                        <span style="font-size: 9px;">{{ substr($date->format('D'), 0, 2) }}</span>
                                    </th>
                                @endfor
                                <th style="min-width: 80px;">Compliance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calendarData as $row)
                            <tr>
                                <td style="text-align: left; white-space: nowrap;">
                                    <strong>{{ Str::limit($row['user']->name, 20) }}</strong>
                                </td>
                                @foreach($row['days'] as $dayNum => $dayData)
                                <td class="{{ $dayData['is_weekend'] ? 'bg-light' : '' }}">
                                    @if($dayData['dar'])
                                        <a href="{{ route('admin.dar.show', $dayData['dar']) }}" title="{{ $dayData['dar']->status_label }}" class="text-decoration-none">
                                            <span class="calendar-cell status-{{ $dayData['dar']->status }}">{{ $dayNum }}</span>
                                        </a>
                                    @else
                                        <span class="calendar-cell status-{{ $dayData['status'] }}">{{ $dayNum }}</span>
                                    @endif
                                </td>
                                @endforeach
                                <td>
                                    @php
                                        $percentage = $row['total_working_days'] > 0
                                            ? round(($row['submitted_count'] / $row['total_working_days']) * 100)
                                            : 0;
                                    @endphp
                                    <div class="progress compliance-bar mb-1">
                                        <div class="progress-bar bg-{{ $percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}"
                                             style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <small>{{ $row['submitted_count'] }}/{{ $row['total_working_days'] }} ({{ $percentage }}%)</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No staff members found</h4>
                    <p class="text-muted">No active staff members match the selected filters.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
