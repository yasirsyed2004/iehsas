<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Task Summary Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 20px; }
        .header { text-align: center; border-bottom: 3px solid #3b82f6; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #3b82f6; margin: 0; font-size: 20px; }
        .header p { color: #666; margin: 5px 0 0; font-size: 12px; }
        .filters { background: #f3f4f6; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #3b82f6; color: white; padding: 8px 6px; text-align: left; font-size: 10px; }
        td { padding: 6px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        tr:nth-child(even) { background: #f9fafb; }
        .badge { padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; display: inline-block; }
        .badge-low { background: #f3f4f6; color: #374151; }
        .badge-medium { background: #dbeafe; color: #1e40af; }
        .badge-high { background: #fef3cd; color: #92400e; }
        .badge-urgent { background: #fee2e2; color: #991b1b; }
        .footer { text-align: center; color: #999; font-size: 9px; margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; }
        .summary { display: flex; gap: 15px; margin-bottom: 15px; }
        .stat { background: #f0f9ff; padding: 8px 15px; border-radius: 5px; text-align: center; display: inline-block; margin-right: 10px; }
        .stat strong { font-size: 16px; color: #3b82f6; }
    </style>
</head>
<body>
    <div class="header">
        <h1>IEHSAS - Task Summary Report</h1>
        <p>Generated on {{ $generatedAt->format('M d, Y h:i A') }}</p>
    </div>

    @if(!empty(array_filter($filters)))
    <div class="filters">
        <strong>Filters Applied:</strong>
        @if(!empty($filters['status'])) Status: {{ ucfirst(str_replace('_', ' ', $filters['status'])) }} | @endif
        @if(!empty($filters['priority'])) Priority: {{ ucfirst($filters['priority']) }} | @endif
        @if(!empty($filters['department_id'])) Department ID: {{ $filters['department_id'] }} | @endif
    </div>
    @endif

    <div>
        <span class="stat">Total: <strong>{{ $tasks->count() }}</strong></span>
        <span class="stat">Completed: <strong>{{ $tasks->where('status', 'completed')->count() }}</strong></span>
        <span class="stat">In Progress: <strong>{{ $tasks->where('status', 'in_progress')->count() }}</strong></span>
        <span class="stat">Overdue: <strong>{{ $tasks->filter(fn($t) => $t->deadline && $t->deadline->isPast() && !in_array($t->status, ['completed', 'closed']))->count() }}</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Department</th>
                <th>Assigned To</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Progress</th>
                <th>Deadline</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $index => $task)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ Str::limit($task->title, 35) }}</td>
                <td>{{ $task->department->name ?? '-' }}</td>
                <td>{{ $task->assignedUser->name ?? '-' }}</td>
                <td><span class="badge badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span></td>
                <td>{{ \App\Models\Task::getStatuses()[$task->status] ?? ucfirst(str_replace('_', ' ', $task->status)) }}</td>
                <td>{{ $task->progress_percentage }}%</td>
                <td>{{ $task->deadline?->format('M d, Y') ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; {{ date('Y') }} IEHSAS - Institute of Engineering, Health Sciences and Applied Sciences</p>
    </div>
</body>
</html>
