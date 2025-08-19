{{-- resources/views/emails/leave_request.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Leave Request Submitted</title>
    {{-- boodstrap link --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


        {{-- add style button --}}

    <style>
        .btn-approve {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        
        .btn-reject {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        
        .btn-cancel {
            background-color: #ffc107;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        
    </style>       
</head>

<body>
    <div class="email-card">
        <p>Request for {{ $leaveRequest->leaveType->name ?? 'leave' }}</p>

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
            I hope this message finds you well. I’m writing to inform you that I would like to request a
            <strong>{{ $leaveRequest->leaveType->name ?? 'leave' }}</strong>.
        </p>

        <p><strong>Request Details:</strong></p>

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

        <p class="mt-4"><strong>You may take action using the buttons below:</strong></p>

        <div class="text-center action-buttons">
            <a href="{{ $acceptUrl }}" class="btn-approve">Approve</a>
            <a href="{{ $rejectUrl }}" class="btn-reject">Reject</a>
            <a href="{{ $cancelUrl }}" class="btn-cancel">Cancel</a>
        </div>

        <p class="footer">
            Thank you for your prompt attention to this matter.<br><br>
            Best regards,<br>
            {{ $leaveRequest->user->name }}
        </p>
    </div>
</body>

</html>
