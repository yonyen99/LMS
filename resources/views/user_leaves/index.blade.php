@extends('layouts.app')

@section('content')
    {{-- My Summary Section --}}
    <div class="card card-1 border-0 p-4 rounded-3 bg-white shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h2 class="fw-bold text-primary m-0">
                <i class="bi bi-bar-chart-fill me-2"></i>My Leave Summary
            </h2>
            <div class="d-flex align-items-center">
                <label for="dateReport" class="me-2 fw-semibold text-muted small">Report Date</label>
                <input type="date" id="dateReport" value="{{ now()->format('Y-m-d') }}"
                    class="form-control shadow-sm rounded-pill" style="width: 160px;">
            </div>
        </div>
    </div>


    <div class="row mb-1 g-3 py-3">
        <div class="col-lg-6">
            <div class="card bg-white h-100 shadow-sm">
                <div class="card-body row g-0">
                    <div class="col-sm-5 pe-sm-4">
                        @php
                            $totalEntitled = $summaries->sum('entitled');
                            $totalTaken = $summaries->sum('taken');
                            $averageUsage = $totalEntitled > 0 ? ($totalTaken / $totalEntitled) * 100 : 0;
                        @endphp
                        <h3 class="text-primary d-flex align-items-center gap-2 mb-2">
                            {{ number_format($averageUsage, 1) }}%
                            <i class="bx bx-calendar icon-30px"></i>
                        </h3>
                        <p class="mb-1">Total {{ $totalEntitled }} days entitled</p>
                        <p class="mb-1">{{ $totalTaken }} days taken across all leave types</p>
                        <span class="badge bg-label-primary">+{{ $summaries->sum('requested') }} Requested</span>
                        <hr class="d-sm-none mt-4">
                    </div>
                    <div class="col-sm-7 ps-sm-4 pt-2">
                        @foreach ($summaries as $counter)
                            @php
                                $percentage = $totalEntitled > 0 ? ($counter->taken / $totalEntitled) * 100 : 0;
                            @endphp
                            <div class="d-flex align-items-center gap-2 mb-2" data-bs-toggle="tooltip"
                                title="{{ $counter->leaveType->name }}: {{ $counter->taken }} / {{ $counter->entitled }} days taken, {{ $counter->requested }} requested">
                                <small class="w-50 text-truncate">{{ $counter->leaveType->name }} ({{ $counter->entitled }})</small>
                                <div class="progress flex-grow-1 bg-label-primary" style="height:8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $percentage }}%;"></div>
                                </div>
                                <small>{{ $counter->taken }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card bg-white h-100 shadow-sm">
                <div class="card-body row g-0">
                    <div class="col-sm-5 pe-sm-4">
                        <h5 class="mb-2">Leave Statistics</h5>
                        <p class="mb-1">
                            <span class="me-2">{{ $summaries->sum('requested') }} New requests</span>
                            <span class="badge bg-label-success">
                                +{{ number_format(($summaries->sum('requested') / ($totalEntitled ?: 1)) * 100, 1) }}%
                            </span>
                        </p>
                        <p class="mb-1">
                            <span class="me-2">{{ $summaries->sum('planned') }} Planned</span>
                            <span class="badge bg-label-secondary">
                                +{{ number_format(($summaries->sum('planned') / ($totalEntitled ?: 1)) * 100, 1) }}%
                            </span>
                        </p>
                        <h6 class="mt-4 fw-normal">
                            <span class="text-success">{{ number_format($averageUsage, 1) }}%</span> Leave taken
                        </h6>
                        <small>Weekly Report</small>
                        <hr class="d-sm-none mt-4">
                    </div>
                    <div class="col-sm-7 ps-sm-4 pt-2">
                        @foreach ($summaries as $counter)
                            @php
                                $total = $counter->entitled ?: 1;
                                $plannedPercent = ($counter->planned / $total) * 100;
                                $requestedPercent = ($counter->requested / $total) * 100;
                            @endphp

                            @if ($counter->planned > 0 || $counter->requested > 0)
                                <div class="mb-4">
                                    <h6 class="fw-semibold mb-1">{{ $counter->leaveType->name }}</h6>

                                    @if ($counter->planned > 0)
                                        <div class="d-flex justify-content-between small">
                                            <span class="text-secondary">Planned: {{ $counter->planned }}</span>
                                            <span class="text-muted">{{ number_format($plannedPercent, 1) }}%</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 6px;">
                                            <div class="progress-bar bg-secondary" style="width: {{ $plannedPercent }}%;"></div>
                                        </div>
                                    @endif

                                    @if ($counter->requested > 0)
                                        <div class="d-flex justify-content-between small">
                                            <span class="text-warning">Requested: {{ $counter->requested }}</span>
                                            <span class="text-muted">{{ number_format($requestedPercent, 1) }}%</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 6px;">
                                            <div class="progress-bar bg-warning text-dark" style="width: {{ $requestedPercent }}%;"></div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-1 border-0 p-4 rounded-3 bg-white"
        style="box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr style="border: 1px solid #000;">
                        <th scope="col" style="padding: 0.75rem; border: 1px solid #d4d4d4;">Leave Type</th>
                        <th scope="col"
                            style="padding: 0.75rem; text-align: center; border: 1px solid #d4d4d4; color: #000000;">
                            Available (Actual)</th>
                        <th scope="col"
                            style="padding: 0.75rem; text-align: center; border: 1px solid #d4d4d4; color: #000000;">
                            Available (Simulated)</th>
                        <th scope="col" style="padding: 0.75rem; text-align: center; border: 1px solid #d4d4d4;">Entitled
                        </th>
                        <th scope="col" style="padding: 0.75rem; text-align: center; border: 1px solid #d4d4d4;">Taken
                        </th>
                        <th scope="col"
                            style="padding: 0.75rem; text-align: center; border: 1px solid #d4d4d4; background: #938f8f; color: #fff;">
                            Planned</th>
                        <th scope="col"
                            style="padding: 0.75rem; text-align: center; border: 1px solid #d4d4d4; background: #F5811E; color: #fff;">
                            Requested</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($summaries as $counter)
                        <tr class="shadow-sm">
                            <td class="fw-medium">{{ $counter->leaveType->name }}</td>
                            <td class="text-center" style="background:#bfbfc148">
                                <span class="badge bg-primary rounded-pill">{{ $counter->available_actual }}</span>
                            </td>
                            <td class="text-center" style="background:#bfbfc148">
                                <span class="badge bg-info rounded-pill">{{ $counter->available_simulated }}</span>
                            </td>
                            <td class="text-center">{{ $counter->entitled }}</td>
                            <td class="text-center">{{ $counter->taken }}</td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $counter->planned }}</span></td>
                            <td class="text-center"><span
                                    class="badge bg-warning text-dark">{{ $counter->requested }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Progress Bars for Visual Representation -->
        <div class="mt-4">
            <h5 class="fw-semibold text-muted mb-3">Leave Balance Overview</h5>
            <div class="col-md-4">
                @php
                    $totalEntitled = $summaries->sum('entitled');
                    $totalTaken = $summaries->sum('taken');
                    $totalPercent = $totalEntitled > 0 ? ($totalTaken / $totalEntitled) * 100 : 0;
                @endphp
                <div class="card p-3 border-0 card-1 bg-white">
                    <h6 class="fw-medium">Total Leave Usage</h6>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalPercent }}%;"
                            aria-valuenow="{{ $totalPercent }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted mt-1">{{ $totalTaken }} / {{ $totalEntitled }} days used</small>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Enable Bootstrap tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
    @endpush
@endsection
