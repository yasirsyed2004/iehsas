<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IEHSAS - DAR Reviewed</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4; }
        .email-container { background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 3px solid #3b82f6; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #3b82f6; margin-bottom: 10px; }
        .info-box { background-color: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; }
        .info-box h4 { margin-top: 0; color: #1e40af; }
        .info-box p { margin: 5px 0; }
        .status-approved { background-color: #dcfce7; border-left: 4px solid #22c55e; }
        .status-revision { background-color: #fef3cd; border-left: 4px solid #f59e0b; }
        .footer { text-align: center; color: #666; font-size: 14px; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">IEHSAS</div>
            <h2>Daily Activity Report {{ $dar->status === 'reviewed' ? 'Approved' : 'Revision Requested' }}</h2>
        </div>

        <p>Dear {{ $dar->user->name }},</p>

        <p>Your Daily Activity Report for <strong>{{ $dar->report_date->format('M d, Y (l)') }}</strong> has been reviewed.</p>

        <div class="info-box {{ $dar->status === 'reviewed' ? 'status-approved' : 'status-revision' }}">
            <h4>Review Status: {{ $dar->status === 'reviewed' ? 'Approved' : 'Revision Requested' }}</h4>
            @if($dar->reviewer)
                <p><strong>Reviewed By:</strong> {{ $dar->reviewer->name }}</p>
            @endif
            <p><strong>Reviewed At:</strong> {{ $dar->reviewed_at->format('M d, Y h:i A') }}</p>
        </div>

        @if($dar->reviewer_comment)
        <div class="info-box">
            <h4>Reviewer Comments</h4>
            <p>{{ $dar->reviewer_comment }}</p>
        </div>
        @endif

        @if($dar->status === 'revision_requested')
        <div class="info-box" style="border-left-color: #f59e0b;">
            <h4>Action Required</h4>
            <p>Please log in to the IEHSAS Staff Portal and update your DAR based on the reviewer's comments. You can add an addendum to provide additional information.</p>
        </div>
        @endif

        <div class="footer">
            <p>This is an automated email from IEHSAS Task Management System.</p>
            <p>Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Institute of Engineering, Health Sciences and Applied Sciences (IEHSAS)</p>
        </div>
    </div>
</body>
</html>
