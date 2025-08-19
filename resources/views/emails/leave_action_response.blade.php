<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('img/logo.avif') }}" type="image/avif">
    <title>Leave Request Confirmation</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #e0f7fa, #f1f8e9);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.6s ease-in-out;
        }

        h1 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .message {
            font-size: 18px;
            margin-bottom: 10px;
            color: #28a745;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .message i {
            color: #28a745;
            font-size: 22px;
            margin-right: 8px;
        }

        .status-text {
            font-size: 16px;
            color: #333;
            margin-bottom: 30px;
        }

        .status {
            font-weight: bold;
            color: {{ $status === 'Accepted' ? '#28a745' : '#dc3545' }};
        }

        .button {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        .button:hover {
            background-color: #0056b3;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <!-- Optional: Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <div class="card">
        <p>Leave Request Action Confirmation</p>
        <div class="message">
            <i class="fas fa-check-circle"></i> {{ $message }}
        </div>
        <p class="status-text">
            The leave request status has been updated to:
            <span class="status">{{ $status }}</span>
        </p>
        <a href="{{ route('leave-requests.index') }}" class="button">Back to Leave Requests</a>
    </div>

</body>

</html>