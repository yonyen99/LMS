@extends('layouts.app')

@section('content')
<div class="card card-1 p-4 bg-white">
    <h2>Calendar of my workmates</h2>
    <p>Leaves of employees having the same line manager</p>

    {{-- Status legend --}}
    <div class="d-flex flex-wrap gap-4 mb-4">
        @php
            $statusColors = [
                'Planned' => '#A59F9F',
                'Accepted' => '#447F44',
                'Requested' => '#FC9A1D',
                'Rejected' => '#F80300',
                'Cancellation' => '#F80300',
                'Canceled' => '#F80300',
            ];
        @endphp

        @foreach ($statusColors as $label => $color)
            <span class="badge" style="background-color: {{ $color }}; color: white; font-size: 0.9rem;">
                {{ $label }}
            </span>
        @endforeach
    </div>

    {{-- Month navigation --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 gap-3">
        @php
            $isToday = $currentDate->format('Y-m') === \Carbon\Carbon::now()->format('Y-m');
        @endphp

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Previous Month --}}
            <form method="GET" class="m-0 p-0">
                <input type="hidden" name="month" value="{{ $currentDate->copy()->subMonth()->month }}">
                <input type="hidden" name="year" value="{{ $currentDate->copy()->subMonth()->year }}">
                <button type="submit" class="btn btn-outline-secondary">&laquo;</button>
            </form>

            {{-- Today --}}
            <form method="GET" class="m-0 p-0">
                <input type="hidden" name="month" value="{{ \Carbon\Carbon::now()->month }}">
                <input type="hidden" name="year" value="{{ \Carbon\Carbon::now()->year }}">
                <button type="submit" class="btn {{ $isToday ? 'btn-primary' : 'btn-outline-secondary' }}">Today</button>
            </form>

            {{-- Next Month --}}
            <form method="GET" class="m-0 p-0">
                <input type="hidden" name="month" value="{{ $currentDate->copy()->addMonth()->month }}">
                <input type="hidden" name="year" value="{{ $currentDate->copy()->addMonth()->year }}">
                <button type="submit" class="btn btn-outline-secondary">&raquo;</button>
            </form>
        </div>

        <h4 class="mb-0 fw-bold text-center text-md-start">
            {{ $currentDate->format('F Y') }}
        </h4>
    </div>


    {{-- Calendar table --}}
    <div class="table-responsive">
        <table class="table table-bordered calendar-table">
            <thead class="table-light text-center">
                <tr>
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <th>{{ $day }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($weeks as $week)
                    <tr class="text-end">
                        @foreach($week as $day)
                            <td class="{{ $day['is_current_month'] ? '' : 'bg-light text-muted' }}" style="vertical-align: top; height: 160px; width: 14.2%;">
                                <div><strong>{{ $day['date']->day }}</strong></div>

                                <div class="user-status-column">
                                    @foreach($day['users'] as $user)
                                        @if(!empty($user['status']))
                                            @php
                                                $statusKey = ucfirst(strtolower(trim($user['status'])));
                                                $bgColor = $statusColors[$statusKey] ?? '#000';
                                                $isHalf = !empty($user['is_half_day']);
                                                $halfDayType = $isHalf ? strtolower($user['half_day_type']) : null;
                                                $halfDayClass = $isHalf ? 'half-day ' . $halfDayType : '';
                                            @endphp

                                            <div class="badge text-white {{ $halfDayClass }}" style="background-color: {{ $bgColor }};">
                                                {{ $user['name'] }}
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
.calendar-table .badge {
    width: 100%;
    padding: 4px;
    font-size: 0.75rem;
    text-align: center;
    border-radius: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-status-column {
    display: flex;
    flex-direction: column;
    gap: 4px;
    width: 100%;
}

.calendar-table td {
    min-width: 110px;
    max-width: 140px;
}

.calendar-table .half-day.am {
    align-self: flex-start;
    width: 48%;
}

.calendar-table .half-day.pm {
    align-self: flex-end;
    width: 48%;
}

@media (max-width: 768px) {
    .calendar-table td {
        min-width: 90px;
    }

    .calendar-table .badge {
        font-size: 0.7rem;
    }
}
</style>

@endsection
