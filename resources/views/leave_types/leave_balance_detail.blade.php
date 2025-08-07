@extends('layouts.app')

@section('title', 'Leave Balance Details')

@section('content')
    <div class="container py-4 shadow-sm bg-white rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary m-0">
                <i class="bi bi-person-circle me-2"></i>
                Leave Balance Details for {{ $user->name }}
            </h2>
            <span class="badge bg-primary">{{ $user->department->name ?? 'No Department' }}</span>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="m-0">Employee Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Department:</strong> {{ $user->department->name ?? 'N/A' }}</p>
                        {{-- <p><strong>Position:</strong> {{ $user->position ?? 'N/A' }}</p> --}}
                    </div>
                </div>
            </div>
        </div>

        <h4 class="mb-3">Leave Summary</h4>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Leave Type</th>
                        <th>Entitled</th>
                        <th>Used</th>
                        <th>Pending</th>
                        <th>Available</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($summaries as $summary)
                        <tr>
                            <td>{{ $summary->leaveType->name }}</td>
                            <td>{{ $summary->entitled }} days</td>
                            <td class="{{ $summary->taken > 0 ? 'text-danger' : 'text-success' }}">{{ $summary->taken }} days</td>
                            <td class="{{ $summary->requested + $summary->planned > 0 ? 'text-warning' : 'text-muted' }}">{{ $summary->requested + $summary->planned }} days</td>
                            <td class="text-success">{{ $summary->available_actual }} days</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a href="{{ route('leave-balances.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to My Leave Balance
            </a>
        </div>
    </div>
@endsection