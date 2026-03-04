<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IEHSAS - DAR Submitted</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4; }
        .email-container { background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 3px solid #3b82f6; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #3b82f6; margin-bottom: 10px; }
        .info-box { background-color: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; }
        .info-box h4 { margin-top: 0; color: #1e40af; }
        .info-box p { margin: 5px 0; }
        .entries-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .entries-table th, .entries-table td { border: 1px solid #e5e7eb; padding: 8px 12px; text-align: left; font-size: 13px; }
        .entries-table th { background-color: #f3f4f6; font-weight: 600; }
        .footer { text-align: center; color: #666; font-size: 14px; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">IEHSAS</div>
            <h2>Daily Activity Report Submitted</h2>
        </div>

        <p>A Daily Activity Report has been submitted for your review.</p>

        <div class="info-box">
            <h4>Report Details</h4>
            <p><strong>Employee:</strong> {{ $dar->user->name }}</p>
            @if($dar->department)
                <p><strong>Department:</strong> {{ $dar->department->name }}</p>
            @endif
            <p><strong>Report Date:</strong> {{ $dar->report_date->format('M d, Y (l)') }}</p>
            <p><strong>Submitted At:</strong> {{ $dar->submitted_at->format('M d, Y h:i A') }}</p>
            <p><strong>Total Activities:</strong> {{ $dar->entries->count() }}</p>
        </div>

        @if($dar->entries->count() > 0)
        <h4>Activity Summary</h4>
        <table class="entries-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Activity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dar->entries as $entry)
                <tr>
                    <td>{{ date('h:i A', strtotime($entry->time_from)) }} - {{ date('h:i A', strtotime($entry->time_to)) }}</td>
                    <td>{{ Str::limit($entry->activity_description, 80) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="info-box">
            <h4>Action Required</h4>
            <p>Please log in to the IEHSAS Admin Panel to review this report.</p>
        </div>

        <div class="footer">
            <p>This is an automated email from IEHSAS Task Management System.</p>
            <p>Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Institute of Engineering, Health Sciences and Applied Sciences (IEHSAS)</p>
        </div>
    </div>
</body>
</html>
