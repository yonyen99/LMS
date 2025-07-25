@extends('layouts.app')

@section('content')
    {{-- My Summary Section --}}
    <div class="card card-1 border-0 p-4 rounded-3" style="background: linear-gradient(135deg, #fbfbfb, #f9f9f9);">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary mb-0" style="font-size: 1.8rem; letter-spacing: 1px;">
                <i class="bi bi-bar-chart-fill me-2"></i>My Leave Summary
            </h2>
            <div class="d-flex align-items-center">
                <label for="dateReport" class="me-2 fw-semibold text-muted small">Report Date</label>
                <input type="date" id="dateReport" value="{{ now()->format('Y-m-d') }}"
                    class="form-control shadow-sm rounded-pill" style="width: 160px; border: 1px solid #ced4da;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr style="border: 1px solid #000;">
                        <th scope="col" style="padding: 0.75rem; border: 1px solid #d4d4d4;">Leave Type</th>
                        <th scope="col"
                            style="padding: 0.75rem; text-align: center; border: 1px solid #d4d4d4; background: #0d6efd; color: #fff;">
                            Available (Actual)</th>
                        <th scope="col"
                            style="padding: 0.75rem; text-align: center; border: 1px solid #d4d4d4; background: #0d6efd; color: #fff;">
                            Available (Simulated)</th>
                        <th scope="col" style="padding: 0.75rem; text-align: center; border: 1px solid #d4d4d4;">Entitled
                        </th>
                        <th scope="col" style="padding: 0.75rem; text-align: center; border: 1px solid #d4d4d4;">Taken</th>
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
                            <td class="text-center" style="background:#0d6dfd48">
                                <span class="badge bg-primary rounded-pill">{{ $counter->available_actual }}</span>
                            </td>
                            <td class="text-center" style="background:#0d6dfd55">
                                <span class="badge bg-info rounded-pill">{{ $counter->available_simulated }}</span>
                            </td>
                            <td class="text-center">{{ $counter->entitled }}</td>
                            <td class="text-center">{{ $counter->taken }}</td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $counter->planned }}</span></td>
                            <td class="text-center"><span class="badge bg-warning text-dark">{{ $counter->requested }}</span></td>
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
                    $totalEntitled = 0;
                    $totalTaken = 0;

                    foreach ($summaries as $counter) {
                        $totalEntitled += $counter->entitled;
                        $totalTaken += $counter->taken;
                    }

                    $totalPercent = $totalEntitled > 0 ? ($totalTaken / $totalEntitled) * 100 : 0;
                @endphp
                <div class="card p-3 border-0 card-1">
                    <h6 class="fw-medium">Total Leave Usage</h6>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-warning"
                            role="progressbar"
                            style="width: {{ $totalPercent }}%;"
                            aria-valuenow="{{ $totalPercent }}"
                            aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted mt-1">{{ $totalTaken }} / {{ $totalEntitled }} days used</small>
                </div>
            </div>
        </div>
    </div>
@endsection
