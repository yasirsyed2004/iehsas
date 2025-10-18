<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IEHSAS Document Status Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 10px;
        }
        .status-badge {
            padding: 15px 25px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
        }
        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }
        .status-pending {
            background-color: #fef3cd;
            color: #92400e;
            border: 2px solid #f59e0b;
        }
        .info-box {
            background-color: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
        }
        .message-box {
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">IEHSAS</div>
            <h2>Document Status Update</h2>
        </div>

        <p>Dear {{ $student->full_name }},</p>

        <p>We have reviewed your enrollment documents and have an update regarding their status:</p>

        @if($status === 'approved')
            <div class="status-badge status-approved">
                ✅ Documents Approved
            </div>
            <p><strong>Congratulations!</strong> All your submitted documents have been approved. Your enrollment process is now complete.</p>
        @elseif($status === 'rejected')
            <div class="status-badge status-rejected">
                ❌ Documents Need Revision
            </div>
            <p>Some of your submitted documents require revision or replacement. Please review the feedback below and resubmit the necessary documents.</p>
        @else
            <div class="status-badge status-pending">
                ⏳ Documents Under Review
            </div>
            <p>Your documents are currently being reviewed by our admissions team. You will receive another update once the review is complete.</p>
        @endif

        <div class="info-box">
            <h4 style="margin-top: 0;">Student Information</h4>
            <p><strong>Name:</strong> {{ $student->full_name }}</p>
            <p><strong>{{ $student->id_type_label }}:</strong> {{ $student->formatted_id }}</p>
            <p><strong>Email:</strong> {{ $student->email }}</p>
        </div>

        @if($message)
            <div class="message-box">
                <h4 style="margin-top: 0;">Additional Comments:</h4>
                <p>{{ $message }}</p>
            </div>
        @endif

        @if($status === 'rejected')
            <h3>Required Actions:</h3>
            <ol>
                <li>Review the feedback provided above</li>
                <li>Prepare the corrected or missing documents</li>
                <li>Access the enrollment portal using the button below</li>
                <li>Re-upload the required documents</li>
                <li>Submit for review again</li>
            </ol>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $enrollmentUrl }}" class="button">Update Documents</a>
            </div>
        @elseif($status === 'approved')
            <div class="info-box">
                <h4 style="margin-top: 0;">What's Next?</h4>
                <p>Your enrollment is now complete! You will receive further communication regarding:</p>
                <ul>
                    <li>Class schedules and timetables</li>
                    <li>Fee payment instructions</li>
                    <li>Orientation program details</li>
                    <li>Student portal access credentials</li>
                </ul>
            </div>
        @endif

        <div class="info-box">
            <h4 style="margin-top: 0;">Need Help?</h4>
            <p>If you have any questions about this status update or need assistance with the enrollment process, please contact our admissions office:</p>
            <p><strong>Email:</strong> {{ config('mail.enrollment.reply_to') }}</p>
            <p><strong>Phone:</strong> +92-XX-XXXXXXXX</p>
        </div>

        <div class="footer">
            <p>This is an automated email from IEHSAS Admissions System.</p>
            <p>Please do not reply to this email. For support, contact {{ config('mail.enrollment.reply_to') }}</p>
            <p>&copy; {{ date('Y') }} Institute of Engineering, Health Sciences and Applied Sciences (IEHSAS)</p>
        </div>
    </div>
</body>
</html>