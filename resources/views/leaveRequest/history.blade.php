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
                        {{-- Row 1: Created --}}
                        <tr>
                            <td class="text-center">➕</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($changs->created_at)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $changs->user->name ?? 'N/A' }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($changs->start_date)->format('d/m/Y') }} ({{ ucfirst($changs->start_time) }})</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($changs->end_date)->format('d/m/Y') }} ({{ ucfirst($changs->end_time) }})</td>
                            <td class="text-center">{{ $changs->reason }}</td>
                            <td class="text-center">{{ number_format($changs->duration, 2) }}</td>
                            <td class="text-center">{{ $changs->leaveType->name ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge bg-warning text-white">Requested</span>
                            </td>
                        </tr>

                        {{-- Row 2: Updated --}}
                        <tr>
                            <td class="text-center">✏️</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($changs->updated_at)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $latestStatusChange->user->name ?? $changs->user->name ?? 'N/A' }}</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center">
                                @php
                                    $status = strtolower($changs->status);
                                    $badgeColor = match ($status) {
                                        'accepted' => 'bg-success',
                                        'rejected', 'canceled', 'cancellation' => 'bg-danger',
                                        'requested' => 'bg-warning',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }} text-white">{{ ucfirst($status) }}</span>
                            </td>
                        </tr>

                        {{-- Row 3: Status Changed --}}
                        <tr>
                            <td class="text-center">➡️</td>
                            <td class="text-center">{{ $latestStatusChange ? \Carbon\Carbon::parse($latestStatusChange->changed_at)->format('d/m/Y') : '-' }}</td>
                            <td class="text-center"></td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($changs->start_date)->format('d/m/Y') }} ({{ ucfirst($changs->start_time) }})</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($changs->end_date)->format('d/m/Y') }} ({{ ucfirst($changs->end_time) }})</td>
                            <td class="text-center">{{ $changs->reason }}</td>
                            <td class="text-center">{{ number_format($changs->duration, 2) }}</td>
                            <td class="text-center">{{ $changs->leaveType->name ?? 'N/A' }}</td>
                            <td class="text-center">
                                @php
                                    $status = strtolower($latestStatusChange->new_status ?? $changs->status);
                                    $badgeColor = match ($status) {
                                        'accepted' => 'bg-success',
                                        'rejected', 'canceled', 'cancellation' => 'bg-danger',
                                        'requested' => 'bg-warning',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }} text-white">{{ ucfirst($status) }}</span>
                            </td>
                        </tr>
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
