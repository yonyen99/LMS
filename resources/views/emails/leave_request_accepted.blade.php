<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Leave Request Approved</title>
</head>
<body>
    @if(isset($forRequester) && $forRequester)
        <p>Hello {{ $leaveRequest->user->name }},</p>
        <p>Your leave request has been <strong>approved</strong>.</p>
    @else
        <p>Hello,</p>
        <p>Your colleague <strong>{{ $leaveRequest->user->name }}</strong> has had their leave request <strong>approved</strong>.</p>
    @endif

    <p><strong>Details:</strong></p>
    <ul>
        <li>Type: {{ $leaveRequest->leaveType->name ?? 'Leave' }}</li>
        <li>Duration: {{ $leaveRequest->duration }} days</li>
        <li>From: {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} ({{ ucfirst($leaveRequest->start_time) }})</li>
        <li>To: {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }} ({{ ucfirst($leaveRequest->end_time) }})</li>
    </ul>

    <p>Approved by: {{ auth()->user()->name }}</p>

    <p>Thanks<br></p>
</body>
</html>
