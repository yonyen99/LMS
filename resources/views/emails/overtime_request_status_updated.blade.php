<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Overtime Request Status Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 10px; text-align: center; }
        .content { padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <p>Overtime Request Status Updated</p>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            <p>Your overtime request has been updated with the following details:</p>
            <ul>
                <li><strong>Employee:</strong> {{ $user->name }}</li>
                <li><strong>Department:</strong> {{ $department->name }}</li>
                <li><strong>Overtime Date:</strong> {{ $overtime->overtime_date }}</li>
                <li><strong>Time Period:</strong> {{ ucwords(str_replace('_', ' ', $overtime->time_period)) }}</li>
                <li><strong>Start Time:</strong> {{ $overtime->start_time }}</li>
                <li><strong>End Time:</strong> {{ $overtime->end_time }}</li>
                <li><strong>Duration:</strong> {{ $overtime->duration }} hour(s)</li>
                <li><strong>Reason:</strong> {{ $overtime->reason ?? 'N/A' }}</li>
                <li><strong>Status:</strong> {{ ucfirst($overtime->status) }}</li>
                <li><strong>Updated At:</strong> {{ $overtime->last_changed_at->format('d/m/Y H:i') }}</li>
            </ul>
            <p>Please contact your manager or HR for any questions.</p>
        </div>
        <div class="footer">
            <p>Best regards,<br>Your HR System</p>
        </div>
    </div>
</body>
</html>