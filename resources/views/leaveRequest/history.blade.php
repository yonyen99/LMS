@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card card-1 shadow border-0">
        <div class="card-header bg-light">
            <h4 class="mb-0">Leave Change History</h4>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm text-sm align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Change Type</th>
                            <th>Changed Date</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reason</th>
                            <th>Duration</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leaveRequests as $change)
                            <tr>
                                <td class="text-center">
                                    @if ($loop->first)
                                        <span title="Created" class="fs-5">➕</span>
                                    @elseif ($change->status === 'Requested')
                                        <span title="Updated" class="fs-5">✏️</span>
                                    @elseif (in_array($change->status, ['Accepted', 'Rejected']))
                                        <span title="Status Changed" class="fs-5">➡️</span>
                                    @else
                                        <span class="fs-5">❓</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($change->last_changed_at ?? $change->updated_at)->format('d/m/Y') }}
                                </td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($change->start_date)->format('d/m/Y') }} ({{ ucfirst($change->start_time) }})
                                </td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($change->end_date)->format('d/m/Y') }} ({{ ucfirst($change->end_time) }})
                                </td>
                                <td class="text-center">{{ $change->reason }}</td>
                                <td class="text-end">{{ number_format($change->duration, 3) }}</td>
                                <td class="text-center">{{ $change->leaveType->name ?? '' }}</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill 
                                        @if ($change->status === 'Accepted') bg-success
                                        @elseif ($change->status === 'Requested') bg-warning text-dark
                                        @elseif ($change->status === 'Rejected') bg-danger
                                        @else bg-secondary
                                        @endif">
                                        {{ $change->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">No history found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer text-end">
            <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary">OK</a>
        </div>
    </div>
</div>
@endsection


{{-- 
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-light">
            <h4 class="mb-0">Leave Change History</h4>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-sm text-sm align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Requested By</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reason</th>
                            <th>Duration</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Requested On</th>
                            <th>Accepted On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td>{{ $leaveRequest->user->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d/m/Y') }} ({{ ucfirst($leaveRequest->start_time) }})</td>
                            <td>{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d/m/Y') }} ({{ ucfirst($leaveRequest->end_time) }})</td>
                            <td>{{ $leaveRequest->reason ?? '-' }}</td>
                            <td>{{ number_format($leaveRequest->duration, 2) }}</td>
                            <td>{{ $leaveRequest->leaveType->name }}</td>
                            <td>
                                <span class="badge rounded-pill 
                                    @if ($leaveRequest->status === 'Accepted') bg-success
                                    @elseif ($leaveRequest->status === 'Requested') bg-warning text-dark
                                    @else bg-secondary
                                    @endif">
                                    {{ $leaveRequest->status }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($leaveRequest->created_at)->format('d/m/Y') }}</td>
                            <td>
                                @if ($leaveRequest->status === 'Accepted')
                                    {{ \Carbon\Carbon::parse($leaveRequest->updated_at)->format('d/m/Y') }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer text-end">
            <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection --}}
