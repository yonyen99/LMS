@extends('layouts.app')

@section('content')
    <!-- Leave Balance Section -->
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
                        @php
                            $totalEntitled = $summaries->sum('entitled');
                            $totalAvailable = $summaries->sum('available_actual');
                            $usagePercent =
                                $totalEntitled > 0 ? (($totalEntitled - $totalAvailable) / $totalEntitled) * 100 : 0;
                        @endphp
                        <h3 class="text-primary d-flex align-items-center gap-2 mb-2">
                            {{ number_format($totalAvailable, 1) }} Days
                            <i class="bx bx-calendar icon-30px"></i>
                        </h3>
                        <p class="mb-1">Total {{ $totalEntitled }} days entitled</p>
                        <p class="mb-1">{{ $totalEntitled - $totalAvailable }} days taken</p>
                        <span class="badge bg-label-primary">+{{ number_format($totalAvailable, 1) }} Available</span>
                        <hr class="d-sm-none mt-4">
                    </div>
                    <div class="col-sm-7 ps-sm-4 pt-2">
                        @forelse ($summaries as $counter)
                            @php
                                $availablePercent =
                                    $counter->entitled > 0
                                        ? ($counter->available_actual / $counter->entitled) * 100
                                        : 0;
                            @endphp
                            <div class="d-flex align-items-center gap-2 mb-2" data-bs-toggle="tooltip"
                                title="{{ $counter->leaveType->name }}: {{ $counter->available_actual }} / {{ $counter->entitled }} days available">
                                <small class="w-50 text-truncate">{{ $counter->leaveType->name }}
                                    ({{ $counter->entitled }})
                                </small>
                                <div class="progress flex-grow-1 bg-label-primary" style="height:8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $availablePercent }}%;"></div>
                                </div>
                                <small>{{ number_format($counter->available_actual, 1) }}</small>
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
                            <span class="me-2">{{ number_format($summaries->sum('available_actual'), 1) }}
                                Available</span>
                            <span class="badge bg-label-success">
                                +{{ number_format(($summaries->sum('available_actual') / ($totalEntitled ?: 1)) * 100, 1) }}%
                            </span>
                        </p>
                        <p class="mb-1">
                            <span class="me-2">{{ number_format($summaries->sum('planned'), 1) }} Planned</span>
                            <span class="badge bg-label-secondary">
                                +{{ number_format(($summaries->sum('planned') / ($totalEntitled ?: 1)) * 100, 1) }}%
                            </span>
                        </p>
                        <h6 class="mt-4 fw-normal">
                            <span class="text-success">{{ number_format($usagePercent, 1) }}%</span> Leave Taken
                        </h6>
                        <small>Monthly Report</small>
                        <hr class="d-sm-none mt-4">
                    </div>
                    <div class="col-sm-7 ps-sm-4 pt-2">
                        @forelse ($summaries as $counter)
                            @php
                                $total = $counter->entitled ?: 1;
                                $availablePercent = ($counter->available_actual / $total) * 100;
                            @endphp
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-1">{{ $counter->leaveType->name }}</h6>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-primary">Available:
                                        {{ number_format($counter->available_actual, 1) }}</span>
                                    <span class="text-muted">{{ number_format($availablePercent, 1) }}%</span>
                                </div>
                                <div class="progress mb-2" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $availablePercent }}%;"></div>
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

    <!-- Employee Requests Section -->
    @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Manager'))
        <div class="card card-1 border-0 p-4 rounded-3 bg-white mt-4"
            style="box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
                <h5 class="fw-bold text-primary m-0">
                    <i class="bi bi-person-lines-fill me-2"></i>Employee Leave Requests
                </h5>
                @if (Auth::user()->hasRole('Admin'))
                    <div class="d-flex align-items-center">
                        <label for="departmentFilter" class="me-2 fw-semibold text-muted small">Filter by Department</label>
                        <select id="departmentFilter" name="department_id" class="form-select shadow-sm rounded-pill"
                            style="width: 200px;">
                            <option value="">All Departments</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
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
                        <tr style="border: 1px solid #000;">
                            <th scope="col" style="padding: 0.75rem; border: 1px solid #d4d4d4;">Employee Name</th>
                            <th scope="col"
                                style="padding: 0.75rem; text-align: center; border: 1px solid #d4d4d4; color: #000000;">
                                Department</th>
                            <th scope="col"
                                style="padding: 0.75rem; text-align: center; border: 1px solid #d4d4d4; background: #F5811E; color: #fff;">
                                Requests Submitted</th>
                            <th scope="col"
                                style="padding: 0.75rem; text-align: center; border: 1px solid #d4d4d4; background: #6c757d; color: #fff;">
                                Day Can request</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employeeRequests as $employee)
                            <tr class="shadow-sm">
                                <td class="fw-medium">{{ $employee->name }}</td>
                                <td class="text-center">{{ $employee->department }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-warning text-dark rounded-pill">{{ $employee->requested_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill">
                                        {{ number_format($summaries->sum('available_actual') - $employee->total_requested, 1) }}
                                        days</span>


                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No employees with leave requests found.
                                </td>
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
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                @if (Auth::user()->hasRole('Admin'))
                    document.getElementById('departmentFilter').addEventListener('change', function() {
                        console.log('Department filter changed to:', this.value);
                        const departmentId = this.value;
                        window.location.href = '{{ route('leave-balances.index') }}' + (departmentId ?
                            '?department_id=' + departmentId : '');
                    });
                @endif
            });
        </script>
    @endpush
@endsection
