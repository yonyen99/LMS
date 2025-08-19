<!DOCTYPE html>
<html>
<head>
    <title>Leave Request Approved</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 15px; text-align: center; }
        .content { padding: 20px; }
        .footer { margin-top: 20px; font-size: 0.9em; color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <p>Leave Request Approved</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $employeeName }},</p>
            
            <p>Your <strong>{{ $leaveType }}</strong> leave request has been approved by <strong>{{ $approverName }}</strong>.</p>
            
            <p><strong>Leave Details:</strong></p>
            <ul>
                <li>Start Date: {{ $startDate }}</li>
                <li>End Date: {{ $endDate }}</li>
                <li>Duration: {{ $duration }} days</li>
            </ul>
            
            <p>If you have any questions, please contact HR.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated notification. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>