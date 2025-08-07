@extends('layouts.app')

@section('title', 'Leave Balance Details')

@section('content')
    <div class="container-fluid py-3 py-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom-0">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div>
                        <h2 class="fw-bold text-primary m-0">
                            <i class="bi bi-person-circle me-2"></i>
                            Leave Balance Details 
                        </h2>
                    </div>
                    
                    @if (Auth::user()->hasAnyRole(['Admin', 'Manager']))
                    <div class="btn-group mt-2 mt-md-0">
                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('leave-balances.export-pdf', $user->id) }}">
                                    <i class="bi bi-file-earmark-pdf me-2"></i> PDF
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('leave-balances.export-excel', $user->id) }}">
                                    <i class="bi bi-file-earmark-excel me-2"></i> Excel
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-muted mb-3">Employee Information</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-2"><strong>Name:</strong></p>
                                        <p class="mb-2"><strong>Email:</strong></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-2">{{ $user->name }}</p>
                                        <p class="mb-2">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-muted mb-3">Employment Details</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-2"><strong>Department:</strong></p>
                                        {{-- <p class="mb-2"><strong>Employee ID:</strong></p> --}}
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-2">{{ $user->department->name ?? 'N/A' }}</p>
                                        {{-- <p class="mb-2">{{ $user->employee_id ?? 'N/A' }}</p> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="mb-3">Leave Summary</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th class="w-25 text-center">Leave Type</th>
                                <th class="text-center">Entitled</th>
                                <th class="text-center">Used</th>
                                <th class="text-center">Pending</th>
                                <th class="text-center">Available</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($summaries as $summary)
                                <tr>
                                    <td>{{ $summary->leaveType->name }}</td>
                                    <td class="text-center">{{ number_format($summary->entitled, 1) }} days</td>
                                    <td class="text-center {{ $summary->taken > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($summary->taken, 1) }} days
                                    </td>
                                    <td class="text-center {{ $summary->requested + $summary->planned > 0 ? 'text-warning' : 'text-muted' }}">
                                        {{ number_format($summary->requested + $summary->planned, 1) }} days
                                    </td>
                                    <td class="text-center text-success">
                                        {{ number_format($summary->available_actual, 1) }} days
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="table-active">
                                <td><strong>Total</strong></td>
                                <td class="text-center"><strong>{{ number_format($summaries->sum('entitled'), 1) }} days</strong></td>
                                <td class="text-center"><strong>{{ number_format($summaries->sum('taken'), 1) }} days</strong></td>
                                <td class="text-center">
                                    <strong>{{ number_format(
                                        $summaries->sum(function ($item) {
                                            return $item->requested + $item->planned;
                                        }),
                                        1,
                                    ) }}
                                    days</strong>
                                </td>
                                <td class="text-center"><strong>{{ number_format($summaries->sum('available_actual'), 1) }}
                                        days</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('leave-balances.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection