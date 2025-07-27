{{-- @extends('layouts.app')

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
                            <th>Changed By</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reason</th>
                            <th>Duration</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leaveRequest->history as $change)
                            <tr>
                                <td class="text-center">
                                    @switch($change->type)
                                        @case('create') <span class="fs-5">➕</span> @break
                                        @case('update') <span class="fs-5">✏️</span> @break
                                        @case('approve') <span class="fs-5">➡️</span> @break
                                        @default <span class="fs-5">❓</span>
                                    @endswitch
                                </td>
                                <td class="text-nowrap">{{ \Carbon\Carbon::parse($change->changed_at)->format('m/d/Y') }}</td>
                                <td class="text-nowrap">{{ $change->changed_by }}</td>
                                <td class="text-nowrap">{{ $change->start_date }} ({{ $change->start_period }})</td>
                                <td class="text-nowrap">{{ $change->end_date }} ({{ $change->end_period }})</td>
                                <td>{{ $change->reason }}</td>
                                <td class="text-end">{{ number_format($change->duration, 3) }}</td>
                                <td>{{ $change->type_name }}</td>
                                <td>
                                    <span class="badge rounded-pill 
                                        @if ($change->status === 'Accepted') bg-success
                                        @elseif ($change->status === 'Requested') bg-warning text-dark
                                        @else bg-secondary
                                        @endif">
                                        {{ $change->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No history found.</td>
                            </tr>
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
@endsection --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-light">
            <h4 class="mb-0">Leave Request Summary</h4>
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
                            <td>{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d/m/Y') }} ({{ $leaveRequest->start_period }})</td>
                            <td>{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d/m/Y') }} ({{ $leaveRequest->end_period }})</td>
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
@endsection
