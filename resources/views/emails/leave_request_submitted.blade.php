<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Leave Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .email-card {
            max-width: 700px;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        }

        h1 {
            color: #0d6efd;
            font-size: 28px;
            margin-bottom: 25px;
            text-align: center;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
        }

        ul {
            padding-left: 0;
            list-style: none;
        }

        ul li {
            padding: 5px 0;
            border-bottom: 1px dashed #ddd;
        }

        ul li strong {
            color: #0d6efd;
        }

        .action-buttons a {
            margin: 0 8px 15px;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
        }

        .btn-approve {
            background-color: #28a745;
            color: #fff;
        }

        .btn-reject {
            background-color: #dc3545;
            color: #fff;
        }

        .btn-cancel {
            background-color: #ffc107;
            color: #000;
        }

        .footer {
            font-size: 14px;
            color: #777;
            text-align: right;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <div class="email-card">
        <h1>New Leave Request Submitted</h1>

        @php
            $adminNames = $admins->pluck('name');
            $displayNames = $adminNames->take(2)->implode(', ');
            $extraCount = $adminNames->count() - 2;
        @endphp

        <p>
            Dear {{ $displayNames }}@if ($extraCount > 0), and {{ $extraCount }} others @endif,
        </p>

        <p>
            A new leave request has been submitted by <strong>{{ $leaveRequest->user->name }}</strong> starting
            <strong>{{ ucfirst($leaveRequest->start_time) }}</strong> for the reason: <em>{{ $leaveRequest->reason }}</em>.
        </p>

        <p><strong>Details:</strong></p>

        <ul>
            <li><strong>Leave Type:</strong> {{ $leaveRequest->leaveType->name ?? 'N/A' }}</li>
            <li><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('F d, Y') }}</li>
            <li><strong>End Date:</strong> {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('F d, Y') }}</li>
            <li><strong>Start Time:</strong> {{ ucfirst($leaveRequest->start_time) }}</li>
            <li><strong>End Time:</strong> {{ ucfirst($leaveRequest->end_time) }}</li>
            <li><strong>Duration:</strong> {{ $leaveRequest->duration }} days</li>
            <li><strong>Status:</strong> {{ ucfirst($leaveRequest->status) }}</li>
        </ul>

        <p class="mt-4">You may take action using the buttons below:</p>

        <div class="text-center action-buttons">
            <a href="{{ $acceptUrl }}" class="btn-approve">Approve</a>
            <a href="{{ $rejectUrl }}" class="btn-reject">Reject</a>
            <a href="{{ $cancelUrl }}" class="btn-cancel">Cancel</a>
        </div>

        <p class="footer">
            Best regards,<br>
            {{ $leaveRequest->user->name }}
        </p>
    </div>
</body>

</html>
