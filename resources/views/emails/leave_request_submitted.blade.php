<!DOCTYPE html>
<html>

<head>
    <title>New Leave Request Submitted</title>
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

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            color: #fff;
            border-radius: 5px;
        }

        .btn-approve {
            background-color: #28a745;
        }

        .btn-reject {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>New Leave Request</h1>

        @php
            $adminNames = $admins->pluck('name');
            $displayNames = $adminNames->take(2)->implode(', ');
            $extraCount = $adminNames->count() - 2;
        @endphp

        <p>
            Dear {{ $displayNames }}@if ($extraCount > 0)
                , and {{ $extraCount }} others
            @endif,
        </p>


        <p>
            I hope this message finds you well.
            I am writing to inform you that I want to request a leave at
            {{ ucfirst($leaveRequest->start_time) }}
            because I have a {{ $leaveRequest->leaveType->name ?? 'N/A' }} with {{ $leaveRequest->reason }}.
        </p>
        <p>Leave request with the following details:
        </p>

        <ul>
            <li><strong>Leave Type:</strong> {{ $leaveRequest->leaveType->name ?? 'N/A' }}</li>
            <li><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('F d, Y') }}
            </li>
            <li><strong>End Date:</strong> {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('F d, Y') }}</li>
            <li><strong>Start Time:</strong> {{ ucfirst($leaveRequest->start_time) }}</li>
            <li><strong>End Time:</strong> {{ ucfirst($leaveRequest->end_time) }}</li>
            <li><strong>Duration:</strong> {{ $leaveRequest->duration }} days</li>
            <li><strong>Reason:</strong> {{ $leaveRequest->reason }}</li>
            <li><strong>Status:</strong> {{ ucfirst($leaveRequest->status) }}</li>
        </ul>

        <p>Please review and respond to this request:</p>

        <a href="{{ $acceptUrl }}" class="btn btn-approve">Approve</a>
        <a href="{{ $rejectUrl }}" class="btn btn-reject">Reject</a>
        <a href="{{ $cancelUrl }}" class="btn btn-reject">Cancel</a>
        <p class="footer">Best regards,<br>{{ $leaveRequest->user->name }}</p>


    </div>
</body>

</html>
