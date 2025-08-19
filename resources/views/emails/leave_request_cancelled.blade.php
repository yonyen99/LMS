<!DOCTYPE html>
<html>
<head>
    <title>Leave Request Cancelled</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #2c3e50;
            font-size: 24px;
        }
        p {
            line-height: 1.6;
            font-size: 16px;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
            font-size: 16px;
        }
        strong {
            color: #2c3e50;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <p>Leave Request Cancelled</p>

        <p>Dear {{ $leaveRequest->user->name }},</p>

        <p>The following leave request has been cancelled:</p>

        <ul>
            <li><strong>Leave Type:</strong> {{ $leaveRequest->leaveType->name ?? 'N/A' }}</li>
            <li><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('F d, Y') }}</li>
            <li><strong>End Date:</strong> {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('F d, Y') }}</li>
            <li><strong>Start Time:</strong> {{ ucfirst($leaveRequest->start_time) }}</li>
            <li><strong>End Time:</strong> {{ ucfirst($leaveRequest->end_time) }}</li>
            <li><strong>Duration:</strong> {{ $leaveRequest->duration }} days</li>
            <li><strong>Reason:</strong> {{ $leaveRequest->reason }}</li>
            <li><strong>Status:</strong> {{ ucfirst($leaveRequest->status) }}</li>
        </ul>

        <p class="footer">Best regards,<br>{{ $leaveRequest->user->name }}</p>
    </div>
</body>
</html>