<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Task Detail - {{ $task->title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 25px; }
        .header { text-align: center; border-bottom: 3px solid #3b82f6; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { color: #3b82f6; margin: 0; font-size: 20px; }
        .header p { color: #666; margin: 5px 0 0; }
        h2 { color: #1e40af; font-size: 15px; border-bottom: 2px solid #dbeafe; padding-bottom: 5px; margin-top: 25px; }
        .info-grid { width: 100%; margin-bottom: 15px; }
        .info-grid td { padding: 6px 10px; vertical-align: top; }
        .info-grid .label { font-weight: bold; color: #666; width: 130px; }
        .info-grid .value { color: #333; }
        .badge { padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; display: inline-block; }
        .badge-low { background: #f3f4f6; color: #374151; }
        .badge-medium { background: #dbeafe; color: #1e40af; }
        .badge-high { background: #fef3cd; color: #92400e; }
        .badge-urgent { background: #fee2e2; color: #991b1b; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th { background: #f3f4f6; padding: 8px; text-align: left; font-size: 11px; border: 1px solid #e5e7eb; }
        table.data td { padding: 6px 8px; border: 1px solid #e5e7eb; font-size: 11px; }
        .description { background: #f9fafb; padding: 12px; border-radius: 5px; border-left: 4px solid #3b82f6; margin: 10px 0; }
        .progress-bar { background: #e5e7eb; border-radius: 10px; height: 12px; margin-top: 5px; }
        .progress-fill { background: #3b82f6; border-radius: 10px; height: 12px; }
        .footer { text-align: center; color: #999; font-size: 9px; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>IEHSAS - Task Detail Report</h1>
        <p>Generated on {{ $generatedAt->format('M d, Y h:i A') }}</p>
    </div>

    <h2>{{ $task->title }}</h2>

    <table class="info-grid">
        <tr>
            <td class="label">Department:</td>
            <td class="value">{{ $task->department->name ?? 'N/A' }}</td>
            <td class="label">Priority:</td>
            <td class="value"><span class="badge badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span></td>
        </tr>
        <tr>
            <td class="label">Status:</td>
            <td class="value">{{ \App\Models\Task::getStatuses()[$task->status] ?? ucfirst(str_replace('_', ' ', $task->status)) }}</td>
            <td class="label">Progress:</td>
            <td class="value">
                {{ $task->progress_percentage }}%
                <div class="progress-bar"><div class="progress-fill" style="width: {{ $task->progress_percentage }}%"></div></div>
            </td>
        </tr>
        <tr>
            <td class="label">Assigned To:</td>
            <td class="value">{{ $task->assignedUser->name ?? 'N/A' }}</td>
            <td class="label">Assigned By:</td>
            <td class="value">{{ $task->assignedByAdmin->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Start Date:</td>
            <td class="value">{{ $task->start_date?->format('M d, Y') ?? 'N/A' }}</td>
            <td class="label">Deadline:</td>
            <td class="value">{{ $task->deadline?->format('M d, Y') ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Created:</td>
            <td class="value">{{ $task->created_at->format('M d, Y h:i A') }}</td>
            <td class="label">Completed:</td>
            <td class="value">{{ $task->completed_at?->format('M d, Y h:i A') ?? '-' }}</td>
        </tr>
    </table>

    @if($task->description)
    <h2>Description</h2>
    <div class="description">{!! nl2br(e($task->description)) !!}</div>
    @endif

    @if($task->expected_deliverables)
    <h2>Expected Deliverables</h2>
    <div class="description">{!! nl2br(e($task->expected_deliverables)) !!}</div>
    @endif

    @if($task->progressLogs->count() > 0)
    <h2>Daily Progress Logs</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Date</th>
                <th>Employee</th>
                <th>Progress</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($task->progressLogs->sortByDesc('log_date') as $log)
            <tr>
                <td>{{ $log->log_date->format('M d, Y') }}</td>
                <td>{{ $log->user->name ?? 'N/A' }}</td>
                <td>{{ $log->progress_percentage }}%</td>
                <td>{{ Str::limit($log->description, 100) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($task->reviews->count() > 0)
    <h2>Manager Reviews</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Date</th>
                <th>Reviewer</th>
                <th>Action</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($task->reviews->sortByDesc('created_at') as $review)
            <tr>
                <td>{{ $review->created_at->format('M d, Y h:i A') }}</td>
                <td>{{ $review->admin->name ?? 'N/A' }}</td>
                <td>{{ $review->action_label }}</td>
                <td>{{ Str::limit($review->remarks, 100) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>&copy; {{ date('Y') }} IEHSAS - Institute of Engineering, Health Sciences and Applied Sciences</p>
    </div>
</body>
</html>
