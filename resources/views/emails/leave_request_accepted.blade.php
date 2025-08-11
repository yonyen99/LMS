<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Leave Request Has Been Accepted</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 5px;">
        <h2 style="color: #447F44;">Leave Request Accepted</h2>
        <p>Dear {{ $user->name }},</p>
        <p>We are pleased to inform you that your leave request has been accepted. Below are the details of your approved leave:</p>

        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 8px; border: 1px solid #e0e0e0; font-weight: bold;">Leave Type</td>
                <td style="padding: 8px; border: 1px solid #e0e0e0;">{{ optional($leaveRequest->leaveType)->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e0e0e0; font-weight: bold;">Start Date & Time</td>
                <td style="padding: 8px; border: 1px solid #e0e0e0;">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} ({{ ucfirst($leaveRequest->start_time) }})</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e0e0e0; font-weight: bold;">End Date & Time</td>
                <td style="padding: 8px; border: 1px solid #e0e0e0;">{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }} ({{ ucfirst($leaveRequest->end_time) }})</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e0e0e0; font-weight: bold;">Duration</td>
                <td style="padding: 8px; border: 1px solid #e0e0e0;">{{ number_format($leaveRequest->duration, 2) }} day(s)</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e0e0e0; font-weight: bold;">Reason</td>
                <td style="padding: 8px; border: 1px solid #e0e0e0;">{{ $leaveRequest->reason ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #e0e0e0; font-weight: bold;">Status</td>
                <td style="padding: 8px; border: 1px solid #e0e0e0;">Accepted</td>
            </tr>
        </table>

        <p>If you have any questions or need further assistance, please contact your manager or HR department.</p>
        <p>Best regards,</p>
        <p>Your HR Team</p>
    </div>
</body>
</html>