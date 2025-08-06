@php
    $totalRequests = $overtimes->count();
    $approvedRequests = $overtimes->where('status', 'approved')->count();
    $pendingRequests = $overtimes->where('status', 'requested')->count();
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Overtime Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .statistics {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .stat-card {
            border: 1px solid #ddd;
            padding: 10px;
            width: 30%;
            text-align: center;
        }
        .stat-card p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            color: white;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; }
        .badge-danger { background-color: #dc3545; }
        .badge-secondary { background-color: #6c757d; }
    </style>
</head>
<body>
    <h1>Overtime Requests</h1>

    <!-- Statistics Cards -->
    <div class="statistics">
        <div class="stat-card">
            <p>Total Requests</p>
            <p><strong>{{ $totalRequests }}</strong></p>
        </div>
        <div class="stat-card">
            <p>Approved</p>
            <p><strong>{{ $approvedRequests }}</strong></p>
        </div>
        <div class="stat-card">
            <p>Pending</p>
            <p><strong>{{ $pendingRequests }}</strong></p>
        </div>
    </div>

    <!-- Overtime Requests Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Department</th>
                <th>Request Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($overtimes as $ot)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $ot->user->name }}</td>
                    <td>{{ $ot->department->name }}</td>
                    <td>{{ $ot->overtime_date }}</td>
                    <td>{{ $ot->start_time }}</td>
                    <td>{{ $ot->end_time }}</td>
                    <td>
                        <span class="badge bg-{{ $ot->status == 'approved' ? 'success' : ($ot->status == 'requested' ? 'warning' : ($ot->status == 'rejected' ? 'danger' : 'secondary')) }}">
                            {{ ucfirst($ot->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">
                        No overtime requests found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>