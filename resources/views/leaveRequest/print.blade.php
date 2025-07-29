<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 10px;
        }
        p.generated-at {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .status {
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 500;
            display: inline-block;
        }
        @media print {
            body {
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <h1>{{ $title }}</h1>
    <p class="generated-at">Generated on {{ $generatedAt }}</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Start Date</th>
                <th>Start Time</th>
                <th>End Date</th>
                <th>End Time</th>
                <th>Reason</th>
                <th>Duration</th>
                <th>Type</th>
                <th>Status</th>
                <th>Requested At</th>
                <th>Last Changed At</th>
            </tr>
        </thead>
        <tbody>
            @if ($leaveRequests->isEmpty())
                <tr>
                    <td colspan="12" class="text-center text-muted">No leave requests found.</td>
                </tr>
            @else
                @foreach ($leaveRequests as $index => $request)
                    @php
                        $displayStatus = ucfirst(strtolower($request->status));
                        $colors = $statusColors[$displayStatus] ?? ['text' => '#000000', 'bg' => '#e0e0e0'];
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ optional($request->user)->name ?? '-' }}</td>
                        <td>{{ optional($request->start_date)->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ ucfirst($request->start_time) }}</td>
                        <td>{{ optional($request->end_date)->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ ucfirst($request->end_time) }}</td>
                        <td>{{ $request->reason ?? '-' }}</td>
                        <td>{{ number_format($request->duration, 2) }}</td>
                        <td>{{ optional($request->leaveType)->name ?? '-' }}</td>
                        <td>
                            <span class="status" style="color: {{ $colors['text'] }}; background-color: {{ $colors['bg'] }};">
                                {{ $displayStatus }}
                            </span>
                        </td>
                        <td>{{ optional($request->requested_at)->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ optional($request->last_changed_at)->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    <div class="no-print">
        <a href="{{ route('leave-requests.index') }}" class="btn btn-primary">Back to Leave Requests</a>
    </div>
</body>
</html>