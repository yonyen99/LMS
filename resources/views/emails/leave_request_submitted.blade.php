<!DOCTYPE html>
<html>
<head>
    <title>New Leave Request Submitted</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { color: #2c3e50; font-size: 24px; }
        p { line-height: 1.6; font-size: 16px; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 10px; font-size: 16px; }
        strong { color: #2c3e50; }
        .footer { margin-top: 20px; font-size: 14px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h1>New Leave Request Submitted (ID: {{ $leaveRequest->id }})</h1>
        <p>Dear {{ $leaveRequest->user->name ?? 'User' }},</p>
        <p>Your leave request has been submitted successfully with the following details:</p>
        <ul>
            <li><strong>Leave Type:</strong> {{ $leaveRequest->leaveType->name ?? 'Unknown' }}</li>
            <li><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('F d, Y') }}</li>
            <li><strong>End Date:</strong> {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('F d, Y') }}</li>
            <li><strong>Start Time:</strong> {{ ucfirst($leaveRequest->start_time ?? 'N/A') }}</li>
            <li><strong>End Time:</strong> {{ ucfirst($leaveRequest->end_time ?? 'N/A') }}</li>
            <li><strong>Duration:</strong> {{ $leaveRequest->duration }} days</li>
            <li><strong>Reason:</strong> {{ $leaveRequest->reason ?? 'N/A' }}</li>
            <li><strong>Status:</strong> {{ ucfirst($leaveRequest->status ?? 'N/A') }}</li>
        </ul>
        <p>Thank you for your submission. You will be notified once your request is reviewed.</p>
        <p class="footer">Best regards,<br>Your LMS Team</p>
    </div>
</body>
</html>