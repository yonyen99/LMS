@extends('layouts.app')

@section('content')
<div class="card shadow-sm rounded-4 p-4 mb-4 bg-white border-0">
    <div class="row">
        <!-- Calendar -->
        <div class="col-md-8">
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h2 class="mb-0">Departments</h2>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <!-- Prev -->
                            <form method="GET" class="m-0 p-0">
                                <input type="hidden" name="month" value="{{ $currentDate->copy()->subMonth()->month }}">
                                <input type="hidden" name="year" value="{{ $currentDate->copy()->subMonth()->year }}">
                                @foreach((array)$selectedDepartmentIds as $deptId)
                                    <input type="hidden" name="departments[]" value="{{ $deptId }}">
                                @endforeach
                                <button type="submit" class="btn btn-outline-secondary">&laquo;</button>
                            </form>

                            <!-- Today -->
                            <form method="GET" class="m-0 p-0">
                                <input type="hidden" name="month" value="{{ now()->month }}">
                                <input type="hidden" name="year" value="{{ now()->year }}">
                                @foreach((array)$selectedDepartmentIds as $deptId)
                                    <input type="hidden" name="departments[]" value="{{ $deptId }}">
                                @endforeach
                                <button type="submit" class="btn {{ $isToday ? 'btn-primary' : 'btn-outline-secondary' }}">Today</button>
                            </form>

                            <!-- Next -->
                            <form method="GET" class="m-0 p-0">
                                <input type="hidden" name="month" value="{{ $currentDate->copy()->addMonth()->month }}">
                                <input type="hidden" name="year" value="{{ $currentDate->copy()->addMonth()->year }}">
                                @foreach((array)$selectedDepartmentIds as $deptId)
                                    <input type="hidden" name="departments[]" value="{{ $deptId }}">
                                @endforeach
                                <button type="submit" class="btn btn-outline-secondary">&raquo;</button>
                            </form>
                        </div>
                    </div>
                    <h5 class="text-muted">{{ $monthName }} {{ $year }}</h5>

                    <div class="d-flex flex-wrap gap-3 mt-3">
                        @foreach($statusColors as $status => $color)
                            <span class="badge" style="background-color: {{ $color }}; color: {{ strtolower($status) === 'requested' ? '#fff' : '#fff' }};">
                                {{ $status }}
                            </span>
                        @endforeach
                    </div>

                    <table class="table table-bordered calendar-grid mt-3">
                        <thead>
                            <tr class="text-center">
                                <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weeks as $week)
                                <tr>
                                    @foreach($week as $date)
                                        @php
                                            $dateStr = $date->format('Y-m-d');
                                            $isMuted = $date->month != $month;
                                        @endphp
                                        <td class="{{ $isMuted ? 'text-muted bg-light' : '' }}">
                                            <div class="day-number">{{ $date->day }}</div>
                                            @if(!empty($events[$dateStr]))
                                                @foreach($events[$dateStr] as $event)
                                                    @php
                                                        $status = $event['status'] ?? 'Planned';
                                                        $bgColor = $statusColors[$status] ?? '#6c757d';
                                                        $textColor = strtolower($status) === 'requested' ? '#fff' : '#fff';
                                                    @endphp
                                                    <div class="event-badge" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                                        {{ $event['title'] }}
                                                        @if(!empty($event['delegation']))
                                                            <span class="badge bg-info ms-1">{{ $event['delegation'] }}</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Filter by Department</h5>
                </div>
                <div class="card-body">
                    <form method="GET" id="department-filter-form">
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">

                        <div class="mb-3">
                            <label>
                                <input type="checkbox" class="department-checkbox" name="departments[]" value="all" {{ in_array('all', (array) $selectedDepartmentIds) ? 'checked' : '' }}>
                                All Departments
                            </label>
                        </div>

                        @foreach($departments as $dept)
                            <div class="mb-2">
                                <label>
                                    <input type="checkbox" class="department-checkbox" name="departments[]" value="{{ $dept->id }}" {{ in_array($dept->id, (array) $selectedDepartmentIds) ? 'checked' : '' }}>
                                    {{ $dept->name }}
                                </label>
                            </div>
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .calendar-grid {
        table-layout: fixed;
    }

    .calendar-grid th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 10px;
    }

    .badge {
        font-size: 14px;
    }

    .calendar-grid td {
        vertical-align: top;
        height: 120px;
        padding: 8px;
        background-color: #fff;
        border: 1px solid #dee2e6;
        transition: background-color 0.2s;
    }

    .calendar-grid td:hover {
        background-color: #f1f3f5;
    }

    .day-number {
        font-weight: bold;
        font-size: 1rem;
        margin-bottom: 6px;
        color: #343a40;
    }

    .event-badge {
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 0.75rem;
        margin-top: 4px;
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .text-muted.bg-light {
        background-color: #f8f9fa !important;
    }

    .card-body label {
        font-weight: 500;
        color: #495057;
    }

    input[type="checkbox"] {
        margin-right: 6px;
    }

    @media (max-width: 768px) {
        .calendar-grid td {
            height: 100px;
            font-size: 0.75rem;
        }

        .event-badge {
            font-size: 0.65rem;
            padding: 2px 6px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('department-filter-form');
        const allCheckbox = document.querySelector('input[name="departments[]"][value="all"]');
        const otherCheckboxes = document.querySelectorAll('input[name="departments[]"]:not([value="all"])');

        if (allCheckbox) {
            allCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    otherCheckboxes.forEach(cb => cb.checked = false);
                }
                form.submit();
            });
        }

        otherCheckboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                if (this.checked && allCheckbox.checked) {
                    allCheckbox.checked = false;
                }
                form.submit();
            });
        });
    });
</script>
@endsection
