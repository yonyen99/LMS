@php
    $statuses = ['Planned', 'Accepted', 'Requested', 'Rejected', 'Cancellation', 'Canceled'];
    $colors = [
        'Planned' => ['text' => '#ffffff', 'bg' => '#A59F9F'],
        'Accepted' => ['text' => '#ffffff', 'bg' => '#447F44'],
        'Requested' => ['text' => '#ffffff', 'bg' => '#FC9A1D'],
        'Rejected' => ['text' => '#ffffff', 'bg' => '#F80300'],
        'Cancellation' => ['text' => '#ffffff', 'bg' => '#F80300'],
        'Canceled' => ['text' => '#ffffff', 'bg' => '#F80300'],
    ];
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .generated-at {
            text-align: right;
            font-size: 10px;
            color: #666;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
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
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="generated-at">Generated at: {{ $generatedAt }}</div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Reason</th>
                <th>Duration</th>
                <th>Type</th>
                <th>Status</th>
                <th>Requested</th>
                <th>Last Change</th>
            </tr>
        </thead>
        <tbody>
            @if ($leaveRequests->isEmpty())
                <tr>
                    <td colspan="9" style="text-align: center; color: #666;">No leave requests found.</td>
                </tr>
            @else
                @foreach ($leaveRequests as $index => $request)
                    @php
                        $displayStatus = ucfirst(strtolower($request->status));
                        $statusColor = $statusColors[$displayStatus] ?? ['text' => '#000000', 'bg' => '#e0e0e0'];
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ optional($request->start_date)->format('d/m/Y') }} ({{ ucfirst($request->start_time) }})</td>
                        <td>{{ optional($request->end_date)->format('d/m/Y') }} ({{ ucfirst($request->end_time) }})</td>
                        <td>{{ $request->reason ?? '-' }}</td>
                        <td>{{ number_format($request->duration, 2) }}</td>
                        <td>{{ optional($request->leaveType)->name ?? '-' }}</td>
                        <td>
                            <span class="status" style="color: {{ $statusColor['text'] }}; background-color: {{ $statusColor['bg'] }};">
                                {{ $displayStatus }}
                            </span>
                        </td>
                        <td>{{ $request->requested_at ? \Carbon\Carbon::parse($request->requested_at)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $request->last_changed_at ? \Carbon\Carbon::parse($request->last_changed_at)->format('d/m/Y') : '-' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</body>
</html>