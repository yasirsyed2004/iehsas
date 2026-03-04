<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DAR - {{ $dar->user->name ?? 'N/A' }} - {{ $dar->report_date->format('M d, Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 25px; }
        .header { text-align: center; border-bottom: 3px solid #3b82f6; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { color: #3b82f6; margin: 0; font-size: 20px; }
        .header p { color: #666; margin: 5px 0 0; }
        h2 { color: #1e40af; font-size: 14px; border-bottom: 2px solid #dbeafe; padding-bottom: 5px; margin-top: 20px; }
        .info-grid { width: 100%; margin-bottom: 15px; }
        .info-grid td { padding: 5px 10px; vertical-align: top; }
        .info-grid .label { font-weight: bold; color: #666; width: 140px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th { background: #f3f4f6; padding: 8px; text-align: left; font-size: 11px; border: 1px solid #e5e7eb; }
        table.data td { padding: 6px 8px; border: 1px solid #e5e7eb; font-size: 11px; }
        .addendum { background: #f0f9ff; padding: 10px; border-left: 3px solid #3b82f6; margin: 8px 0; }
        .footer { text-align: center; color: #999; font-size: 9px; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>IEHSAS - Daily Activity Report</h1>
        <p>Generated on {{ $generatedAt->format('M d, Y h:i A') }}</p>
    </div>

    <h2>Report Information</h2>
    <table class="info-grid">
        <tr>
            <td class="label">Employee:</td>
            <td>{{ $dar->user->name ?? 'N/A' }}</td>
            <td class="label">Report Date:</td>
            <td>{{ $dar->report_date->format('l, M d, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Department:</td>
            <td>{{ $dar->department->name ?? 'N/A' }}</td>
            <td class="label">Status:</td>
            <td>{{ $dar->status_label }}</td>
        </tr>
        <tr>
            <td class="label">Submitted At:</td>
            <td>{{ $dar->submitted_at?->format('M d, Y h:i A') ?? '-' }}</td>
            <td class="label">Reviewed By:</td>
            <td>{{ $dar->reviewer->name ?? '-' }}</td>
        </tr>
    </table>

    <h2>Activity Entries</h2>
    @if($dar->entries->count() > 0)
    <table class="data">
        <thead>
            <tr>
                <th>#</th>
                <th>Time From</th>
                <th>Time To</th>
                <th>Activity Description</th>
                <th>Related Task</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dar->entries as $index => $entry)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ date('h:i A', strtotime($entry->time_from)) }}</td>
                <td>{{ date('h:i A', strtotime($entry->time_to)) }}</td>
                <td>{{ $entry->activity_description }}</td>
                <td>{{ $entry->relatedTask?->title ?? '-' }}</td>
                <td>{{ $entry->remarks ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No activity entries recorded.</p>
    @endif

    @if($dar->addendums->count() > 0)
    <h2>Addendums</h2>
    @foreach($dar->addendums as $addendum)
    <div class="addendum">
        <p style="margin: 0 0 5px;">{{ $addendum->content }}</p>
        <small style="color: #666;">By: {{ $addendum->addedBy->name ?? 'Unknown' }} | {{ $addendum->created_at->format('M d, Y h:i A') }}</small>
    </div>
    @endforeach
    @endif

    @if($dar->reviewer_comment)
    <h2>Reviewer Comment</h2>
    <div class="addendum">{{ $dar->reviewer_comment }}</div>
    @endif

    <div class="footer">
        <p>&copy; {{ date('Y') }} IEHSAS - Institute of Engineering, Health Sciences and Applied Sciences</p>
    </div>
</body>
</html>
