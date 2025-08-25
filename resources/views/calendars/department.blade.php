@extends('layouts.app')

@section('content')
<div class="card shadow-sm rounded-4 p-2 p-md-4 bg-white border-0">
    <div class="row flex-column flex-md-row">

        <!-- Calendar -->
        <div class="col-md-10">
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-body p-2 p-md-4">
                    {{-- Header & navigation --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h2 class="mb-0 fs-4">Departments</h2>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            {{-- Prev Month --}}
                            <form method="GET" class="m-0 p-0">
                                <input type="hidden" name="month" value="{{ $currentDate->copy()->subMonth()->month }}">
                                <input type="hidden" name="year" value="{{ $currentDate->copy()->subMonth()->year }}">
                                @foreach((array)$selectedDepartmentIds as $deptId)
                                    <input type="hidden" name="departments[]" value="{{ $deptId }}">
                                @endforeach
                                <button type="submit" class="btn btn-outline-secondary">&laquo;</button>
                            </form>

                            {{-- Today --}}
                            <form method="GET" class="m-0 p-0">
                                <input type="hidden" name="month" value="{{ now()->month }}">
                                <input type="hidden" name="year" value="{{ now()->year }}">
                                @foreach((array)$selectedDepartmentIds as $deptId)
                                    <input type="hidden" name="departments[]" value="{{ $deptId }}">
                                @endforeach
                                <button type="submit" class="btn {{ $isToday ? 'btn-primary' : 'btn-outline-secondary' }}">Today</button>
                            </form>

                            {{-- Next Month --}}
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

                    {{-- Status legend --}}
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        @foreach($statusColors as $status => $color)
                            <span class="badge" style="background-color: {{ $color }}; color:#fff;">
                                {{ $status }}
                            </span>
                        @endforeach
                    </div>

                    {{-- Calendar table --}}
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered calendar-grid">
                            <thead>
                                <tr class="text-center text-sm">
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

                                                $cellClasses = $isMuted ? 'text-muted bg-light' : '';
                                                if(!empty($events[$dateStr])) {
                                                    foreach($events[$dateStr] as $event) {
                                                        if(isset($event['type']) && $event['type'] === 'non_working') {
                                                            $cellClasses .= ' bg-light text-secondary';
                                                            break;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <td class="{{ $cellClasses }}">
                                                <div class="day-number {{ $dateStr === now()->toDateString() ? 'text-danger' : '' }}">
                                                    {{ $date->day }}
                                                </div>

                                                @if(!empty($events[$dateStr]))
                                                    @foreach($events[$dateStr] as $event)
                                                        @php
                                                            $status = $event['status'] ?? 'Planned';
                                                            $bgColor = $statusColors[$status] ?? '#6c757d';
                                                        @endphp
                                                        <div class="event-badge"
                                                            style="background-color: {{ $bgColor }}; color:#fff; cursor:pointer;"
                                                            data-event='@json($event)'
                                                            data-bs-toggle="tooltip"
                                                            title="{{ $status }} {{ !empty($event['delegation']) ? ' - ' . $event['delegation'] : '' }}">
                                                            {{ $event['title'] }}
                                                            @if(!empty($event['delegation']))
                                                                <span class="badge bg-info ms-1">{{ $event['delegation'] }}</span>
                                                            @endif
                                                            @if(isset($event['type']) && $event['type'] === 'event')
                                                                <span class="badge bg-secondary ms-1"></span>
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
        </div>

        <!-- Sidebar: Department filter -->
        <div class="col-md-2">
            <div class="card shadow-sm rounded-4 border-0 mt-3 mt-md-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Filter Departments</h5>
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

{{-- Event Detail Modal --}}
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventDetailModalLabel">Event Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul id="event-detail-list" class="list-group"></ul>
      </div>
    </div>
  </div>
</div>

{{-- Styles --}}
<style>
    .calendar-grid { table-layout: fixed; }
    .calendar-grid th { background-color: #f8f9fa; font-weight: 600; font-size: 0.9rem; padding: 8px; }
    .calendar-grid td { vertical-align: top; height: 120px; padding: 8px; border: 1px solid #dee2e6; transition: background-color 0.2s; }
    .calendar-grid td:hover { background-color: #f1f3f5; }
    .day-number { font-weight: bold; font-size: 1rem; margin-bottom: 6px; color: #343a40; }
    .event-badge { padding: 3px 8px; border-radius: 20px; font-size: 0.75rem; margin-top: 4px; display: inline-block; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .text-muted.bg-light { background-color: #f8f9fa !important; }
    .card-body label { font-weight: 500; color: #495057; }
    input[type="checkbox"] { margin-right: 6px; }
    @media (max-width: 768px) {
        .calendar-grid td { height: 90px; font-size: 0.7rem; padding: 4px; }
        .day-number { font-size: 0.85rem; }
        .event-badge { font-size: 0.65rem; padding: 2px 5px; }
        .card-header h5 { font-size: 1rem; }
    }
</style>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Enable Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    // Click event for event badges
    document.querySelectorAll('.event-badge').forEach(function(badge) {
        badge.addEventListener('click', function() {
            var eventData = JSON.parse(this.dataset.event);
            var list = document.getElementById('event-detail-list');
            list.innerHTML = ''; // Clear previous

            for (var key in eventData) {
                if (eventData[key]) {
                    var li = document.createElement('li');
                    li.className = 'list-group-item';
                    li.textContent = key + ': ' + eventData[key];
                    list.appendChild(li);
                }
            }

            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
            modal.show();
        });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
