<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IEHSAS - New Task Assigned</title>
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
        .priority-badge {
            padding: 15px 25px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
        }
        .priority-urgent {
            background-color: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }
        .priority-high {
            background-color: #fef3cd;
            color: #92400e;
            border: 2px solid #f59e0b;
        }
        .priority-medium {
            background-color: #dbeafe;
            color: #1e40af;
            border: 2px solid #3b82f6;
        }
        .priority-low {
            background-color: #f3f4f6;
            color: #374151;
            border: 2px solid #9ca3af;
        }
        .info-box {
            background-color: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box h4 {
            margin-top: 0;
            color: #1e40af;
        }
        .info-box p {
            margin: 5px 0;
        }
        .deadline-warning {
            background-color: #fef3cd;
            border-left: 4px solid #f59e0b;
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
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">IEHSAS</div>
            <h2>New Task Assigned</h2>
        </div>

        <p>Dear {{ $user->name }},</p>

        <p>A new task has been assigned to you. Please review the details below:</p>

        <div class="priority-badge priority-{{ $task->priority }}">
            {{ ucfirst($task->priority) }} Priority Task
        </div>

        <div class="info-box">
            <h4>Task Details</h4>
            <p><strong>Title:</strong> {{ $task->title }}</p>
            @if($task->description)
                <p><strong>Description:</strong> {{ Str::limit($task->description, 300) }}</p>
            @endif
            @if($task->department)
                <p><strong>Department:</strong> {{ $task->department->name }}</p>
            @endif
            <p><strong>Priority:</strong> {{ ucfirst($task->priority) }}</p>
            <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $task->status)) }}</p>
            <p><strong>Assigned By:</strong> {{ $assignedByName }}</p>
            <p><strong>Created:</strong> {{ $task->created_at->format('M d, Y h:i A') }}</p>
        </div>

        @if($task->deadline)
            <div class="deadline-warning">
                <h4 style="margin-top: 0; color: #92400e;">Deadline</h4>
                <p><strong>{{ $task->deadline->format('M d, Y') }}</strong></p>
                @php
                    $daysLeft = now()->diffInDays($task->deadline, false);
                @endphp
                @if($daysLeft > 0)
                    <p>You have <strong>{{ ceil($daysLeft) }} day(s)</strong> to complete this task.</p>
                @elseif($daysLeft == 0)
                    <p style="color: #dc2626;"><strong>This task is due today!</strong></p>
                @else
                    <p style="color: #dc2626;"><strong>This task is overdue!</strong></p>
                @endif
            </div>
        @endif

        @if($task->expected_deliverables)
            <div class="info-box">
                <h4>Expected Deliverables</h4>
                <p>{{ $task->expected_deliverables }}</p>
            </div>
        @endif

        <div class="info-box">
            <h4>Need Help?</h4>
            <p>If you have any questions about this task, please contact your reporting manager or the admin who assigned this task.</p>
        </div>

        <div class="footer">
            <p>This is an automated email from IEHSAS Task Management System.</p>
            <p>Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Institute of Engineering, Health Sciences and Applied Sciences (IEHSAS)</p>
        </div>
    </div>
</body>
</html>
