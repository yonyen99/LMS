<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        p {
            text-align: right;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>

<body>
    <h2>{{ $title }}</h2>
    <p>Generated at: {{ $generatedAt }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Employee</th>
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
            @forelse ($leaveRequests as $index => $request)
                @php
                    $startDate = $request->start_date
                        ? \Carbon\Carbon::parse($request->start_date)->format('d/m/Y')
                        : '-';
                    $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') : '-';
                    $requestedAt = $request->requested_at
                        ? \Carbon\Carbon::parse($request->requested_at)->format('d/m/Y')
                        : '-';
                    $lastChangedAt = $request->last_changed_at
                        ? \Carbon\Carbon::parse($request->last_changed_at)->format('d/m/Y')
                        : '-';

                    $displayStatus = ucfirst(strtolower($request->status));
                    $colors = $statusColors[$displayStatus] ?? ['text' => '#000', 'bg' => '#e0e0e0'];

                    // ✅ Same logic as your notification code
                    $reason = $request->reason_type === 'Other' ? $request->other_reason : $request->reason_type;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ optional($request->user)->name ?? '-' }}</td>
                    <td>{{ $startDate }} ({{ ucfirst($request->start_time) }})</td>
                    <td>{{ $endDate }} ({{ ucfirst($request->end_time) }})</td>
                    <td>{{ $reason ?? '-' }}</td> {{-- ✅ fixed --}}
                    <td>{{ number_format($request->duration, 2) }}</td>
                    <td>{{ optional($request->leaveType)->name ?? '-' }}</td>
                    <td>
                        <span class="badge"
                            style="color: {{ $colors['text'] }}; background-color: {{ $colors['bg'] }};">
                            {{ $displayStatus }}
                        </span>
                    </td>
                    <td>{{ $requestedAt }}</td>
                    <td>{{ $lastChangedAt }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">No leave requests found.</td>
                </tr>
            @endforelse

        </tbody>
    </table>
</body>

</html>
