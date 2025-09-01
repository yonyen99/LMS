@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Monthly Limit Card -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card bg-white shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="text-primary mb-2">
                                    <i class="bi bi-calendar-month me-2"></i>Monthly Leave Limit
                                </h5>
                                <div class="d-flex align-items-center gap-4">
                                    <div class="bg-primary text-white rounded-3 p-3 text-center" style="width: 120px;">
                                        <h4 class="mb-0">{{ number_format($monthlyRemaining, 1) }}</h4>
                                        <small>Days Left This Month</small>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Monthly Usage</span>
                                            <span class="fw-semibold">{{ number_format($currentMonthUsage, 1) }} / {{ $monthlyLimit }} days</span>
                                        </div>
                                        <div class="progress" style="height: 12px;">
                                            <div class="progress-bar {{ $currentMonthUsage >= $monthlyLimit ? 'bg-danger' : 'bg-success' }}" 
                                                 style="width: {{ $monthlyUsagePercentage }}%">
                                                <span class="progress-text">{{ number_format($monthlyUsagePercentage, 1) }}%</span>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                @if($monthlyRemaining <= 0)
                                                    <span class="text-danger">⚠️ You've reached your monthly limit</span>
                                                @else
                                                    <span class="text-success">✅ {{ $monthlyRemaining }} days available this month</span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mt-3 mt-md-0">
                                <div class="alert alert-info">
                                    <small>
                                        <i class="bi bi-info-circle me-1"></i>
                                        <strong>Monthly Policy:</strong> Maximum {{ $monthlyLimit }} days per calendar month.
                                        Includes all leave types. Resets on the 1st of each month.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rest of your existing content -->
        <div class="card card-1 border-0 p-4 rounded-3 bg-white shadow-sm">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <h2 class="fw-bold text-primary m-0">
                    <i class="bi bi-calendar-check-fill me-2"></i>Leave Balance
                </h2>
                <div class="d-flex align-items-center">
                    <label for="balanceDate" class="me-2 fw-semibold text-muted small">As of Date</label>
                    <input type="date" id="balanceDate" value="{{ now()->format('Y-m-d') }}"
                        class="form-control shadow-sm rounded-pill" style="width: 160px;">
                </div>
            </div>
        </div>

        <div class="row mb-1 g-3 py-3">
            <!-- Summary Card -->
            <div class="col-lg-6">
                <div class="card bg-white h-100 shadow-sm">
                    <div class="card-body row g-0">
                        <div class="col-sm-5 pe-sm-4">
                            <h3 class="text-primary d-flex align-items-center gap-2 mb-2">
                                {{ number_format($totals['available'], 1) }} Days
                                <i class="bx bx-calendar icon-30px"></i>
                            </h3>
                            <p class="mb-1">Total {{ $totals['entitled'] }} days entitled</p>
                            <p class="mb-1">{{ $totals['taken'] }} days taken</p>
                            <p class="mb-1">{{ $totals['requested'] }} days requested</p>
                            <span class="badge bg-label-primary">+{{ $totals['available'] }} Available</span>
                            <hr class="d-sm-none mt-4">
                        </div>
                        <div class="col-sm-7 ps-sm-4 pt-2 scrollable-leave-types">
                            @forelse ($summaries as $summary)
                                @php
                                    $entitled = is_object($summary) ? $summary->entitled ?? 0 : $summary['entitled'] ?? 0;
                                    $taken = is_object($summary) ? $summary->taken ?? 0 : $summary['taken'] ?? 0;
                                    $requested = is_object($summary) ? $summary->requested ?? 0 : $summary['requested'] ?? 0;
                                    $planned = is_object($summary) ? $summary->planned ?? 0 : $summary['planned'] ?? 0;
                                    $available_actual = max($entitled - $taken, 0);
                                    $available_simulated = max($entitled - ($taken + $requested), 0);

                                    if (is_object($summary)) {
                                        $leaveTypeName = is_object($summary->leaveType ?? null)
                                            ? $summary->leaveType->name
                                            : $summary->leaveType['name'] ?? 'Unknown Leave Type';
                                    } else {
                                        $leaveTypeName = is_object($summary['leaveType'] ?? null)
                                            ? $summary['leaveType']->name
                                            : $summary['leaveType']['name'] ?? 'Unknown Leave Type';
                                    }

                                    $availablePercent = $entitled > 0 ? ($available_actual / $entitled) * 100 : 0;
                                @endphp
                                <div class="d-flex align-items-center gap-2 mb-2" data-bs-toggle="tooltip"
                                    title="{{ $leaveTypeName }}: {{ $available_actual }} available out of {{ $entitled }} days ({{ $taken }} taken, {{ $requested }} requested)">
                                    <small class="w-50 text-truncate">{{ $leaveTypeName }}
                                        ({{ $entitled }})
                                    </small>
                                    <div class="progress flex-grow-1 bg-label-primary" style="height:8px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $availablePercent }}%;">
                                        </div>
                                    </div>
                                    <small>{{ number_format($available_actual, 1) }}</small>
                                </div>
                            @empty
                                <div class="alert alert-warning py-2">
                                    <p class="text-muted mb-0">No leave balances available.</p>
                                    <small class="text-muted">Please contact HR if you believe this is an error.</small>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance Overview Card -->
            <div class="col-lg-6">
                <div class="card bg-white h-100 shadow-sm">
                    <div class="card-body row g-0">
                        <div class="col-sm-5 pe-sm-4">
                            <h5 class="mb-2">Balance Overview</h5>
                            <p class="mb-1">
                                <span class="me-2">{{ number_format($totals['available'], 1) }} Available</span>
                                <span class="badge bg-label-success">
                                    +{{ $totals['entitled'] > 0 ? number_format(($totals['available'] / $totals['entitled']) * 100, 1) : 0 }}%
                                </span>
                            </p>
                            <p class="mb-1">
                                <span class="me-2">{{ $totals['requested'] }} Requested</span>
                                <span class="badge bg-label-warning">
                                    {{ $totals['entitled'] > 0 ? number_format(($totals['requested'] / $totals['entitled']) * 100, 1) : 0 }}%
                                </span>
                            </p>
                            <p class="mb-1">
                                <span class="me-2">{{ $summaries->sum('planned') }} Planned</span>
                                <span class="badge bg-label-info">
                                    {{ $totals['entitled'] > 0 ? number_format(($summaries->sum('planned') / $totals['entitled']) * 100, 1) : 0 }}%
                                </span>
                            </p>
                            <h6 class="mt-3 fw-normal">
                                <span class="text-success">
                                    {{ $totals['entitled'] > 0 ? number_format(($totals['taken'] / $totals['entitled']) * 100, 1) : 0 }}%
                                </span> Leave Taken
                            </h6>
                            <small>As of {{ now()->format('M j, Y') }}</small>
                            <hr class="d-sm-none mt-4">
                        </div>
                        <div class="col-sm-7 ps-sm-4 pt-2 scrollable-leave-types">
                            @forelse ($summaries as $summary)
                                @php
                                    $entitled = is_object($summary) ? $summary->entitled ?? 0 : $summary['entitled'] ?? 0;
                                    $taken = is_object($summary) ? $summary->taken ?? 0 : $summary['taken'] ?? 0;
                                    $requested = is_object($summary) ? $summary->requested ?? 0 : $summary['requested'] ?? 0;
                                    $available_actual = max($entitled - $taken, 0);

                                    if (is_object($summary)) {
                                        $leaveTypeName = is_object($summary->leaveType ?? null)
                                            ? $summary->leaveType->name
                                            : $summary->leaveType['name'] ?? 'Unknown Leave Type';
                                    } else {
                                        $leaveTypeName = is_object($summary['leaveType'] ?? null)
                                            ? $summary['leaveType']->name
                                            : $summary['leaveType']['name'] ?? 'Unknown Leave Type';
                                    }

                                    $total = $entitled ?: 1;
                                    $availablePercent = ($available_actual / $total) * 100;
                                    $takenPercent = ($taken / $total) * 100;
                                    $requestedPercent = ($requested / $total) * 100;
                                @endphp
                                <div class="mb-3">
                                    <h6 class="fw-semibold mb-1">{{ $leaveTypeName }}</h6>
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-primary">Available:
                                            {{ number_format($available_actual, 1) }}</span>
                                        <span class="text-muted">{{ number_format($availablePercent, 1) }}%</span>
                                    </div>
                                    <div class="progress mb-1" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: {{ $availablePercent }}%;"
                                            data-bs-toggle="tooltip"
                                            title="Available: {{ number_format($available_actual, 1) }} days">
                                        </div>
                                        <div class="progress-bar bg-warning" style="width: {{ $requestedPercent }}%;"
                                            data-bs-toggle="tooltip"
                                            title="Requested: {{ number_format($requested, 1) }} days">
                                        </div>
                                        <div class="progress-bar bg-danger" style="width: {{ $takenPercent }}%;"
                                            data-bs-toggle="tooltip" title="Taken: {{ number_format($taken, 1) }} days">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span>Entitled: {{ $entitled }}</span>
                                        <span>Taken: {{ $taken }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-warning py-2">
                                    <p class="text-muted mb-0">No leave balances available.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Manager'))
            <div class="card card-1 border-0 p-4 rounded-3 bg-white mt-4 shadow">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
                    <h5 class="fw-bold text-primary m-0">
                        <i class="bi bi-person-lines-fill me-2"></i>
                        @if (Auth::user()->hasRole('Admin'))
                            All Employees
                        @else
                            My Team
                        @endif Leave Overview
                    </h5>
                    @if (Auth::user()->hasRole('Admin'))
                        <div class="d-flex align-items-center">
                            <label for="departmentFilter" class="me-2 fw-semibold text-muted small">Filter by
                                Department</label>
                            <select id="departmentFilter" name="department_id" class="form-select shadow-sm rounded-pill"
                                style="width: 200px;">
                                <option value="">All Departments</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th class="text-center">Department</th>
                                <th class="text-center">Entitled</th>
                                <th class="text-center">Used</th>
                                <th class="text-center">Available</th>
                                <th class="text-center">Monthly Used</th>
                                <th class="text-center">Utilization</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($departmentOverview as $employee)
                                <tr class="{{ $employee->is_manager ?? false ? 'table-info' : '' }}">
                                    <td>
                                        {{ $employee->name }}
                                        @if ($employee->is_manager ?? false)
                                            <span class="badge bg-primary ms-2">Manager</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $employee->department }}</td>
                                    <td class="text-center">{{ number_format($employee->entitled, 1) }}</td>
                                    <td class="text-center">{{ number_format($employee->used, 1) }}</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill {{ ($employee->available ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ number_format($employee->available, 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill {{ ($employee->monthly_used ?? 0) >= 1.5 ? 'bg-danger' : (($employee->monthly_used ?? 0) > 0 ? 'bg-warning' : 'bg-success') }}">
                                            {{ number_format($employee->monthly_used, 1) }} / 1.5
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{ number_format($employee->utilization, 1) }}%
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('leave-balances.show', $employee->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No employees found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize tooltips
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });

                    @if (Auth::user()->hasRole('Admin'))
                        document.getElementById('departmentFilter').addEventListener('change', function() {
                            const departmentId = this.value;
                            window.location.href = '{{ route('leave-balances.index') }}' +
                                (departmentId ? '?department_id=' + departmentId : '');
                        });
                    @endif
                });
            </script>
        @endpush
    </div>
@endsection

<style>
    .scrollable-leave-types {
        max-height: 300px;
        overflow-y: auto;
        padding-right: 8px;
    }

    .scrollable-leave-types::-webkit-scrollbar {
        width: 6px;
    }

    .scrollable-leave-types::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .scrollable-leave-types::-webkit-scrollbar-thumb {
        background: #a0a0a0;
        border-radius: 10px;
    }

    .scrollable-leave-types::-webkit-scrollbar-thumb:hover {
        background: #808080;
    }

    .scrollable-leave-types {
        scrollbar-width: thin;
        scrollbar-color: #a0a0a0 #f1f1f1;
    }

    .scrollable-leave-types {
        mask-image: linear-gradient(to bottom, black 90%, transparent 100%);
    }

    .progress-bar .progress-text {
        position: absolute;
        right: 5px;
        font-size: 10px;
        color: white;
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
</style>