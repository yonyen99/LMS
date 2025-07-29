@extends('layouts.app')

@section('content')
<div class="container py-4" style="width: 40%">
    <div class="card shadow rounded border-0">
        <div class="card-header text-white" style="background-color: green">
            <h4 class="mb-0">
                <i class="bi bi-file-text me-2"></i>
                Leave Request Details
            </h4>
        </div>

        <div class="card-body">
            <dl class="row mb-4" style="margin-left: 25px;">

                <dt class="col-sm-5 text-muted">Leave Type</dt>
                <dd class="col-sm-7 fs-5">{{ $leaveRequest->leaveType->name ?? '-' }}</dd>

                <dt class="col-sm-5 text-muted">Duration (days)</dt>
                <dd class="col-sm-7 fs-5">{{ $leaveRequest->duration }}</dd>

                <dt class="col-sm-5 text-muted">Start Date & Time</dt>
                <dd class="col-sm-7 fs-5">
                    {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }}
                    <span class="badge bg-info text-white ms-2 text-capitalize">{{ $leaveRequest->start_time }}</span>
                </dd>

                <dt class="col-sm-5 text-muted">End Date & Time</dt>
                <dd class="col-sm-7 fs-5">
                    {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}
                    <span class="badge bg-info text-white ms-2 text-capitalize">{{ $leaveRequest->end_time }}</span>
                </dd>

                <dt class="col-sm-5 text-muted">Reason</dt>
                <dd class="col-sm-7 fs-5"><pre class="mb-0" style="white-space: pre-wrap;">{{ $leaveRequest->reason ?? '-' }}</pre></dd>

                <dt class="col-sm-5 text-muted">Status</dt>
                <dd class="col-sm-7 fs-5">
                    @php
                        $statusColors = [
                            'planned' => ['text' => 'white', 'bg' => 'secondary'],
                            'accepted' => ['text' => 'white', 'bg' => 'success'],
                            'requested' => ['text' => 'white', 'bg' => 'warning'],
                            'rejected' => ['text' => 'white', 'bg' => 'danger'],
                            'cancellation' => ['text' => 'white', 'bg' => 'danger'],
                            'canceled' => ['text' => 'white', 'bg' => 'danger'],
                        ];

                        $status = strtolower($leaveRequest->status);
                        $badge = $statusColors[$status] ?? ['text' => 'dark', 'bg' => 'light'];
                    @endphp

                    <span class="badge bg-{{ $badge['bg'] }} text-{{ $badge['text'] }} text-capitalize fs-6">
                        {{ ucfirst($leaveRequest->status) }}
                    </span>
                </dd>
            </dl>

            <div class="d-flex gap-2"  style="margin-left: 35px;">
                <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
