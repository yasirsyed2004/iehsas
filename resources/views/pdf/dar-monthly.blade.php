<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Monthly DAR Compliance - {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9px; color: #333; margin: 15px; }
        .header { text-align: center; border-bottom: 3px solid #3b82f6; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { color: #3b82f6; margin: 0; font-size: 18px; }
        .header p { color: #666; margin: 3px 0 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #3b82f6; color: white; padding: 4px 2px; text-align: center; font-size: 8px; }
        td { padding: 3px 2px; text-align: center; border: 1px solid #e5e7eb; font-size: 8px; }
        td.name { text-align: left; padding-left: 5px; font-weight: bold; white-space: nowrap; }
        .cell { width: 16px; height: 16px; border-radius: 3px; display: inline-block; line-height: 16px; font-size: 7px; font-weight: bold; }
        .cell-reviewed { background: #198754; color: white; }
        .cell-submitted { background: #0dcaf0; color: white; }
        .cell-revision_requested { background: #ffc107; color: #000; }
        .cell-draft { background: #6c757d; color: white; }
        .cell-missing { background: #dc3545; color: white; }
        .cell-weekend { background: #e9ecef; color: #adb5bd; }
        .cell-future { background: #f8f9fa; color: #ccc; }
        .legend { margin-bottom: 10px; font-size: 9px; }
        .legend span { padding: 2px 6px; border-radius: 3px; margin-right: 8px; }
        .footer { text-align: center; color: #999; font-size: 8px; margin-top: 15px; border-top: 1px solid #eee; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>IEHSAS - Monthly DAR Compliance Report</h1>
        <p>{{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }} | Generated on {{ $generatedAt->format('M d, Y h:i A') }}</p>
    </div>

    <div class="legend">
        <strong>Legend:</strong>
        <span class="cell cell-reviewed">R</span> Reviewed
        <span class="cell cell-submitted">S</span> Submitted
        <span class="cell cell-revision_requested">V</span> Revision
        <span class="cell cell-draft">D</span> Draft
        <span class="cell cell-missing">X</span> Missing
        <span class="cell cell-weekend">W</span> Weekend
    </div>

    <table>
        <thead>
            <tr>
                <th style="text-align: left; min-width: 100px;">Employee</th>
                @for($day = 1; $day <= $daysInMonth; $day++)
                    <th>{{ $day }}</th>
                @endfor
                <th>Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($calendarData as $row)
            <tr>
                <td class="name">{{ Str::limit($row['user']->name, 18) }}</td>
                @foreach($row['days'] as $dayNum => $dayData)
                <td>
                    @if($dayData['dar'])
                        @php
                            $code = match($dayData['dar']->status) {
                                'reviewed' => 'R',
                                'submitted' => 'S',
                                'revision_requested' => 'V',
                                'draft' => 'D',
                                default => '-',
                            };
                        @endphp
                        <span class="cell cell-{{ $dayData['dar']->status }}">{{ $code }}</span>
                    @elseif($dayData['is_weekend'])
                        <span class="cell cell-weekend">W</span>
                    @elseif($dayData['date']->isPast())
                        <span class="cell cell-missing">X</span>
                    @else
                        <span class="cell cell-future">-</span>
                    @endif
                </td>
                @endforeach
                <td>
                    @php
                        $pct = $row['total_working_days'] > 0
                            ? round(($row['submitted_count'] / $row['total_working_days']) * 100)
                            : 0;
                    @endphp
                    <strong>{{ $pct }}%</strong><br>
                    <small>{{ $row['submitted_count'] }}/{{ $row['total_working_days'] }}</small>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; {{ date('Y') }} IEHSAS - Institute of Engineering, Health Sciences and Applied Sciences</p>
    </div>
</body>
</html>
