<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Leave Request Approved - {{ config('app.name') }}</title>
</head>
<body>
    <p>Dear {{ $employeeName }},</p>

    <p>Your leave request has been <strong>approved</strong> with the following details:</p>
    
    <ul>
        <li><strong>Leave Type:</strong> {{ $leaveType }}</li>
        <li><strong>Start Date:</strong> {{ $startDate }}</li>
        <li><strong>End Date:</strong> {{ $endDate }}</li>
        <li><strong>Duration:</strong> {{ $duration }} days</li>
        <li><strong>Approved By:</strong> {{ $approverName }}</li>
    </ul>

    @if(!empty($leaveRequest->notes))
        <p><strong>Notes:</strong> {{ $leaveRequest->notes }}</p>
    @endif

    <p>You can view your leave request at any time by logging into the system.</p>

    <p>Best regards,<br>
    {{ config('app.name') }} Team</p>
</body>
</html>
