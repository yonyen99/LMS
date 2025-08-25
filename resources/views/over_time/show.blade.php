@extends('layouts.app')

@section('title', 'Overtime Request Details')

@section('content')
    <div class="container my-5">
        <div class="card shadow-sm border">
            <div class="card-body">
                <!-- Header -->
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('over-time.index') }}" class="text-primary me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="h4 fw-bold text-dark mb-0">Overtime Details</h1>
                    <span class="ms-auto px-3 py-1 rounded-pill small fw-semibold 
                        @if($overtime->status === 'approved') bg-success text-white
                        @elseif($overtime->status === 'rejected') bg-danger text-white
                        @elseif($overtime->status === 'cancelled') bg-secondary text-white
                        @else bg-warning text-dark @endif">
                        {{ ucfirst($overtime->status) }}
                    </span>
                </div>

                <!-- Request Summary -->
                <div class="row align-items-center border-bottom pb-3 mb-3">
                    <div class="col-md-6">
                        <h5 class="mb-1">Request #{{ str_pad($overtime->id, 5, '0', STR_PAD_LEFT) }}</h5>
                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('F j, Y') }}</p>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <p class="text-muted mb-1 small">Submitted by</p>
                        <p class="fw-medium mb-0">{{ $overtime->user->name }}</p>
                    </div>
                </div>

                <!-- Details -->
                <div class="row g-4 border-bottom pb-3 mb-3">
                    <!-- Time details -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3">Time Details</h6>
                        <p class="text-muted small mb-1">Period</p>
                        <p class="fw-medium">{{ ucwords(str_replace('_', ' ', $overtime->time_period)) }}</p>

                        <div class="row">
                            <div class="col">
                                <p class="text-muted small mb-1">Start</p>
                                <p class="fw-medium">{{ $overtime->start_time }}</p>
                            </div>
                            <div class="col">
                                <p class="text-muted small mb-1">End</p>
                                <p class="fw-medium">{{ $overtime->end_time }}</p>
                            </div>
                        </div>

                        <p class="text-muted small mb-1">Duration</p>
                        <p class="fw-medium">{{ $overtime->duration }} hour(s)</p>
                    </div>

                    <!-- Employee details -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3">Employee Details</h6>
                        <p class="text-muted small mb-1">Department</p>
                        <p class="fw-medium">{{ $overtime->department->name ?? 'N/A' }}</p>

                        <p class="text-muted small mb-1">Email</p>
                        <p class="fw-medium">{{ $overtime->user->email }}</p>

                        <p class="text-muted small mb-1">Requested At</p>
                        <p class="fw-medium">
                            {{ \Carbon\Carbon::parse($overtime->requested_at)->format('M j, Y g:i A') }}
                        </p>
                    </div>
                </div>

                <!-- Reason -->
                <div class="border-bottom pb-3 mb-3">
                    <h6 class="fw-bold text-primary mb-3">Reason</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $overtime->reason }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div>
                    <div class="d-flex flex-wrap gap-2">
                        @if (auth()->user()->hasAnyRole(['Manager', 'Admin']) && $overtime->status === 'requested')
                            <form action="{{ route('over-time.accept', $overtime->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm fw-semibold">
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('over-time.reject', $overtime->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm fw-semibold">
                                    Reject
                                </button>
                            </form>
                        @endif

                        @if (
                            (auth()->user()->hasAnyRole(['Manager', 'Admin']) ||
                                auth()->id() === $overtime->user_id) &&
                                $overtime->status !== 'cancelled')
                            <form action="{{ route('over-time.cancel', $overtime->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm fw-semibold">
                                    Cancel
                                </button>
                            </form>
                        @endif

                        @if (auth()->id() === $overtime->user_id && $overtime->status === 'requested')
                            <a href="{{ route('over-time.edit', $overtime->id) }}" class="btn btn-primary btn-sm fw-semibold">
                                Edit
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection