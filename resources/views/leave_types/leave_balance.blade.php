@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card card-1 border-0 p-4 rounded-3 bg-white shadow-sm">
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
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
                            <span class="badge bg-label-primary">+{{ $totals['available'] }} Available</span>
                            <hr class="d-sm-none mt-4">
                        </div>
                        <div class="col-sm-7 ps-sm-4 pt-2 scrollable-leave-types">
                            @forelse ($summaries as $summary)
                                @php
                                    // Debug: Check what's in the summary
// dd($summary); // Remove this after debugging

$entitled = is_object($summary) ? $summary->entitled : $summary['entitled'] ?? 0;
$available_actual = is_object($summary)
    ? $summary->available_actual
    : $summary['available_actual'] ?? 0;
$leaveTypeName = is_object($summary)
    ? (is_object($summary->leaveType)
        ? $summary->leaveType->name
        : 'Unknown')
    : $summary['leaveType']['name'] ?? 'Unknown';

                                    $availablePercent = $entitled > 0 ? ($available_actual / $entitled) * 100 : 0;
                                @endphp
                                <div class="d-flex align-items-center gap-2 mb-2" data-bs-toggle="tooltip"
                                    title="{{ $leaveTypeName }}: {{ $available_actual }} / {{ $entitled }} days available">
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
                                <p class="text-muted">No leave balances available.</p>
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
                                <span class="me-2">{{ $summaries->sum('planned') }} Planned</span>
                                <span class="badge bg-label-secondary">
                                    +{{ $totals['entitled'] > 0 ? number_format(($summaries->sum('planned') / $totals['entitled']) * 100, 1) : 0 }}%
                                </span>
                            </p>
                            <h6 class="mt-4 fw-normal">
                                <span class="text-success">
                                    {{ $totals['entitled'] > 0 ? number_format(($totals['taken'] / $totals['entitled']) * 100, 1) : 0 }}%
                                </span> Leave Taken
                            </h6>
                            <small>Monthly Report</small>
                            <hr class="d-sm-none mt-4">
                        </div>
                        <div class="col-sm-7 ps-sm-4 pt-2 scrollable-leave-types">
                            @forelse ($summaries as $summary)
                                @php
                                    $entitled = is_object($summary) ? $summary->entitled : $summary['entitled'] ?? 0;
                                    $available_actual = is_object($summary)
                                        ? $summary->available_actual
                                        : $summary['available_actual'] ?? 0;
                                    $leaveTypeName = is_object($summary)
                                        ? (is_object($summary->leaveType)
                                            ? $summary->leaveType->name
                                            : 'Unknown')
                                        : $summary['leaveType']['name'] ?? 'Unknown';

                                    $total = $entitled ?: 1;
                                    $availablePercent = ($available_actual / $total) * 100;
                                @endphp
                                <div class="mb-4">
                                    <h6 class="fw-semibold mb-1">{{ $leaveTypeName }}</h6>
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-primary">Available:
                                            {{ number_format($available_actual, 1) }}</span>
                                        <span class="text-muted">{{ number_format($availablePercent, 1) }}%</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $availablePercent }}%;">
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No leave balances available.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Manager'))
            <div class="card card-1 border-0 p-4 rounded-3 bg-white mt-4 shadow">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
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
                                        <span
                                            class="badge rounded-pill {{ ($employee->available ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ number_format($employee->available, 1) }}
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
                                    <td colspan="6" class="text-center text-muted">No employees found</td>
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
    @endsection

    <style>
        /* Custom scrollbar styling */
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

        /* For Firefox */
        .scrollable-leave-types {
            scrollbar-width: thin;
            scrollbar-color: #a0a0a0 #f1f1f1;
        }

        /* Optional fade effect at bottom */
        .scrollable-leave-types {
            mask-image: linear-gradient(to bottom, black 90%, transparent 100%);
        }
    </style>
