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
                            <th class="text-center px-1 py-2">Change Type</th>
                            <th class="text-center px-1 py-2">Changed Date</th>
                            <th class="text-center px-1 py-2">Changed By</th>
                            <th class="text-center px-1 py-2">Start Date</th>
                            <th class="text-center px-1 py-2">End Date</th>
                            <th class="text-center px-12 py-12">Reason</th>
                            <th class="text-center px-1 py-2">Duration</th>
                            <th class="text-center px-1 py-2">Type</th>
                            <th class="text-center px-1 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Row 1: Created --}}
                        <tr>
                            <td class="text-center px-1 py-2">➕</td>
                            <td class="text-center px-1 py-2">{{ \Carbon\Carbon::parse($changs->created_at)->format('d/m/Y') }}</td>
                            <td class="text-center px-1 py-2">{{ $changs->user->name ?? 'N/A' }}</td>
                            <td class="text-center px-1 py-2">
                                {{ \Carbon\Carbon::parse($changs->start_date)->format('d/m/Y') }}<br>
                                <span>({{ ucfirst($changs->start_time) }})</span>
                            </td>
                            <td class="text-center px-1 py-2">
                                {{ \Carbon\Carbon::parse($changs->end_date)->format('d/m/Y') }} <br>
                                <span>({{ ucfirst($changs->end_time) }})</span>
                            </td>
                            <td class="text-center px-12 py-12">{{ $changs->reason }}</td>
                            <td class="text-center px-1 py-2">{{ number_format($changs->duration, 1) }}</td>
                            <td class="text-center px-1 py-2">{{ $changs->leaveType->name ?? 'N/A' }}</td>
                            <td class="text-center px-1 py-2">
                                <span class="badge bg-warning text-white" style="font-size: 14px">Requested</span>
                            </td>
                        </tr>

                        {{-- Row 2: Updated --}}
                        <tr>
                            <td class="text-center px-1 py-2">✏️</td>
                            <td class="text-center px-1 py-2">{{ \Carbon\Carbon::parse($changs->updated_at)->format('d/m/Y') }}</td>
                            <td class="text-center px-1 py-2">{{ $latestStatusChange->user->name ?? $changs->user->name ?? 'N/A' }}</td>
                            <td class="text-center px-1 py-2"></td>
                            <td class="text-center px-1 py-2"></td>
                            <td class="text-center px-12 py-12"></td>
                            <td class="text-center px-1 py-2"></td>
                            <td class="text-center px-1 py-2"></td>
                            <td class="text-center px-1 py-2">
                                @php
                                    $status = strtolower($changs->status);
                                    $badgeColor = match ($status) {
                                        'accepted' => 'bg-success',
                                        'rejected', 'canceled', 'cancellation' => 'bg-danger',
                                        'requested' => 'bg-warning',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }} text-white" style="font-size: 14px">{{ ucfirst($status) }}</span>
                            </td>
                        </tr>

                        {{-- Row 3: Status Changed --}}
                        <tr>
                            <td class="text-center px-1 py-2">➡️</td>
                            <td class="text-center px-1 py-2"></td>
                            <td class="text-center px-1 py-2"></td>
                            <td class="text-center px-1 py-2">
                                {{ \Carbon\Carbon::parse($changs->start_date)->format('d/m/Y') }}<br>
                                <span>({{ ucfirst($changs->start_time) }})</span>
                            </td>
                            <td class="text-center px-1 py-2">
                                {{ \Carbon\Carbon::parse($changs->end_date)->format('d/m/Y') }} <br>
                                <span>({{ ucfirst($changs->end_time) }})</span>
                            </td>
                            <td class="text-center px-12 py-12">{{ $changs->reason }}</td>
                            <td class="text-center px-1 py-2">{{ number_format($changs->duration, 1) }}</td>
                            <td class="text-center px-1 py-2">{{ $changs->leaveType->name ?? 'N/A' }}</td>
                            <td class="text-center px-1 py-2">
                                @php
                                    $status = strtolower($latestStatusChange->new_status ?? $changs->status);
                                    $badgeColor = match ($status) {
                                        'accepted' => 'bg-success',
                                        'rejected', 'canceled', 'cancellation' => 'bg-danger',
                                        'requested' => 'bg-warning',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }} text-white" style="font-size: 14px" >{{ ucfirst($status) }}</span>
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
