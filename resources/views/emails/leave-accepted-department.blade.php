<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #17a2b8; color: white; padding: 15px; text-align: center; }
        .content { padding: 20px; border: 1px solid #ddd; }
        .footer { margin-top: 20px; font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Leave Notice: {{ $requesterName }}</h1>
        </div>
        <div class="content">
            <p>Dear Team Member,</p>
            
            <p>This is to inform you that {{ $requesterName }}'s leave request has been approved by {{ $approverName }}:</p>
            
            <p><strong>Leave Details:</strong></p>
            <ul>
                <li><strong>Type:</strong> {{ $leaveType }}</li>
                <li><strong>Dates:</strong> {{ $startDate }} to {{ $endDate }}</li>
                <li><strong>Duration:</strong> {{ $duration }} days</li>
            </ul>

            <p>Please plan accordingly for any coverage needed during this period.</p>
            
            <div class="footer">
                <p>Best regards,<br>{{ config('app.name') }}</p>
            </div>
        </div>
    </div>
</body>
</html>