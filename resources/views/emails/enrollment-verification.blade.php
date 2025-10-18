<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IEHSAS Enrollment Verification</title>
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
        .verification-code {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            margin: 10px 0;
        }
        .info-box {
            background-color: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
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
        .warning {
            background-color: #fef3cd;
            border: 1px solid #faebcd;
            color: #664d03;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">IEHSAS</div>
            <h2>Enrollment Form Verification</h2>
        </div>

        <p>Dear {{ $student->full_name }},</p>

        <p>Congratulations! You have successfully passed the entry test and are now eligible to proceed with the enrollment process.</p>

        <div class="verification-code">
            <h3 style="margin: 0; font-size: 18px;">Your Verification Code</h3>
            <div class="code">{{ $student->enrollment_verification_code }}</div>
            <p style="margin: 10px 0 0 0; font-size: 14px;">Valid for {{ $expiryHours }} hours</p>
        </div>

        <div class="info-box">
            <h4 style="margin-top: 0;">Student Information</h4>
            <p><strong>Name:</strong> {{ $student->full_name }}</p>
            <p><strong>{{ $student->id_type_label }}:</strong> {{ $student->formatted_id }}</p>
            <p><strong>Email:</strong> {{ $student->email }}</p>
            <p><strong>Contact:</strong> {{ $student->contact_number }}</p>
        </div>

        <h3>Next Steps:</h3>
        <ol>
            <li>Click the button below to access the enrollment form</li>
            <li>Enter your {{ $student->id_type_label }} and the 4-digit verification code above</li>
            <li>Upload the required documents:
                <ul>
                    <li>CNIC/Form-B/Passport Copy</li>
                    <li>Educational Certificates</li>
                    <li>CV/Resume</li>
                    <li>Recent Photograph</li>
                </ul>
            </li>
            <li>Submit your enrollment form</li>
        </ol>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verificationUrl }}" class="button">Complete Enrollment Form</a>
        </div>

        <div class="warning">
            <strong>Important:</strong> This verification code will expire in {{ $expiryHours }} hours. If you miss this deadline, you will need to contact the admissions office to request a new code.
        </div>

        <div class="info-box">
            <h4 style="margin-top: 0;">Need Help?</h4>
            <p>If you have any questions or face any issues during the enrollment process, please contact our admissions office:</p>
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