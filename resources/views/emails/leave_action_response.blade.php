<!DOCTYPE html>
<html>
<head>
    <title>Leave Request Action Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50;
            font-size: 24px;
            text-align: center;
        }
        p {
            line-height: 1.6;
            font-size: 16px;
            text-align: center;
        }
        .status {
            font-weight: bold;
            color: {{ $status === 'Accepted' ? '#28a745' : '#dc3545' }};
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Leave Request Action Confirmation</h1>
        <p>{{ $message }}</p>
        <p>The leave request status has been updated to: <span class="status">{{ $status }}</span></p>
        <a href="{{ route('leave-requests.index') }}" class="button">Back to Leave Requests</a>
        <p class="footer">Best regards,<br>Leave Management System</p>
    </div>
</body>
</html>