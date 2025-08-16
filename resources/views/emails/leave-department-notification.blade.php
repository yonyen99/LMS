<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Department Notification: Leave Approved</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 0.9em;
            color: #777;
        }
        .leave-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn-view {
            display: inline-block;
            background-color: #0d6efd;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .employee-info {
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2>{{ config('app.name') }} - Leave Management System</h2>
        </div>

        <h3>Leave Request Approved in {{ $leaveRequest->user->department->name }}</h3>
        
        <div class="employee-info">
            <p>{{ $leaveRequest->user->name }} ({{ $leaveRequest->user->email }}) has had their leave request approved by {{ $approverName }}.</p>
        </div>

        <div class="leave-details">
            <h4>Leave Details</h4>
            <ul class="list-unstyled">
                <li><strong>Type:</strong> {{ $leaveRequest->leaveType->name ?? 'N/A' }}</li>
                <li><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('F d, Y') }}</li>
                <li><strong>End Date:</strong> {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('F d, Y') }}</li>
                <li><strong>Duration:</strong> {{ $leaveRequest->duration }} day(s)</li>
                <li><strong>Reason:</strong> {{ $leaveRequest->reason }}</li>
                <li><strong>Status:</strong> <span class="text-success">Approved</span></li>
            </ul>
        </div>

        <div class="text-center">
            <a href="{{ route('leave-requests.show', $leaveRequest->id) }}" class="btn-view">
                View Full Leave Details
            </a>
        </div>

        <div class="footer">
            <p>You're receiving this notification because you're a member of the {{ $leaveRequest->user->department->name }} department.</p>
            <p>If this doesn't concern you, please ignore this email.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>