<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Overtime Request</title>
    <style>
        /* body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 10px; text-align: center; }
        .content { padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #777; } */
        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 4px 2px;
            font-size: 14px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
        }
        .btn-view { background-color: #0d6efd; }
        .btn-edit { background-color: #6c757d; }
        .btn-cancel { background-color: #ffc107; }
        .btn-approve { background-color: #198754; }
        .btn-reject { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <p>New Overtime Request Submitted</p>
        </div>
        <div class="content">
            <p>A new overtime request has been submitted with the following details:</p>
            <ul>
                <li><strong>Employee:</strong> {{ $user->name }}</li>
                <li><strong>Department:</strong> {{ $department->name ?? 'N/A' }}</li>
                <li><strong>Overtime Date:</strong> {{ $overtime->overtime_date }}</li>
                <li><strong>Time Period:</strong> {{ ucwords(str_replace('_', ' ', $overtime->time_period)) }}</li>
                <li><strong>Start Time:</strong> {{ $overtime->start_time }}</li>
                <li><strong>End Time:</strong> {{ $overtime->end_time }}</li>
                <li><strong>Duration:</strong> {{ $overtime->duration }} hour(s)</li>
                <li><strong>Reason:</strong> {{ $overtime->reason ?? 'N/A' }}</li>
                <li><strong>Status:</strong> {{ ucfirst($overtime->status) }}</li>
                <li><strong>Submitted At:</strong> {{ $overtime->requested_at->format('d/m/Y H:i') }}</li>
            </ul>

            <p><strong>Available Actions:</strong></p>

            <!-- Always allow viewing -->
            <a href="{{ route('over-time.show', $overtime->id) }}" class="btn btn-view">View Details</a>

            <!-- Actions for owner -->
            @if (auth()->user()->id === $overtime->user_id && $overtime->status === 'requested')
                <a href="{{ route('over-time.edit', $overtime->id) }}" class="btn btn-edit">Edit</a>
                <a href="{{ route('over-time.cancel', $overtime->id) }}" class="btn btn-cancel">Cancel</a>
            @endif

            <!-- Actions for Managers/Admins -->
            @if (auth()->user()->hasAnyRole(['Manager', 'Admin']) && $overtime->status === 'requested')
                <a href="{{ route('over-time.accept', $overtime->id) }}" class="btn btn-approve">Approve</a>
                <a href="{{ route('over-time.reject', $overtime->id) }}" class="btn btn-reject">Reject</a>
            @endif

            <p>Please review the request and take appropriate action.</p>
        </div>
        <div class="footer">
            <p>Best regards,<br>{{ $user->name }}</p>
        </div>
    </div>
</body>
</html>
