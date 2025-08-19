@extends('layouts.app')

@section('content')
<div class="card card-1 p-4">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h2 class="fw-bold">Yearly calendar <span class="text-muted" style="font-size: 22px">( {{ Auth::user()->name }} )</span></h2>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-center gap-3 mb-3">
        <!-- Legend -->
        <div class="d-flex flex-wrap gap-4">
            @php
                $statusColors = [
                    'Planned' => ['color' => '#A59F9F', 'icon' => 'P', 'title' => 'Planned Leave'],
                    'Accepted' => ['color' => '#447F44', 'icon' => '✓', 'title' => 'Accepted Leave'],
                    'Requested' => ['color' => '#FC9A1D', 'icon' => '?', 'title' => 'Requested Leave'],
                    'Rejected' => ['color' => '#F80300', 'icon' => '✗', 'title' => 'Rejected Leave'],
                    'Cancellation' => ['color' => '#F80300', 'icon' => 'C', 'title' => 'Cancellation Request'],
                    'Canceled' => ['color' => '#F80300', 'icon' => 'X', 'title' => 'Canceled Leave'],
                ];
            @endphp

            @foreach ($statusColors as $label => $data)
                <span class="badge d-flex align-items-center gap-1" style="background-color: {{ $data['color'] }}; color: white; font-size: 0.9rem;">
                    <span style="font-weight: bold; font-size: 0.8rem;">{{ $data['icon'] }}</span>
                    {{ $label }}
                </span>
            @endforeach
            
            <!-- Additional legend items -->
            <span class="badge d-flex align-items-center gap-1" style="background-color: #00d2f8; color: white; font-size: 0.9rem;">
                <span style="font-weight: bold; font-size: 0.8rem;">I</span>
                Invalid Day
            </span>
            <span class="badge d-flex align-items-center gap-1" style="background-color: #ba0000; color: white; font-size: 0.9rem;">
                <span style="font-weight: bold; font-size: 0.8rem;">H</span>
                Holiday
            </span>
        </div>

        @php
            $currentYear = $year ?? now()->year;
        @endphp

        <div class="d-flex justify-content-center align-items-center gap-2 my-3">
            <form method="GET" action="">
                <input type="hidden" name="year" value="{{ $currentYear - 1 }}">
                <button type="submit" class="btn btn-primary btn-sm">&lt; {{ $currentYear - 1 }}</button>
            </form>

            <div class="fw-bold fs-5">{{ $currentYear }}</div>

            <form method="GET" action="">
                <input type="hidden" name="year" value="{{ $currentYear + 1 }}">
                <button type="submit" class="btn btn-primary btn-sm">{{ $currentYear + 1 }} &gt;</button>
            </form>
        </div>
    </div>

    <style>
        .calendar-table {
            table-layout: fixed;
            border-collapse: collapse;
            width: 100%;
        }

        .calendar-table th,
        .calendar-table td {
            width: 28px;
            height: 28px;
            padding: 0;
            margin: 0;
            border: 1px solid #ccc;
            position: relative;
        }

        .calendar-table th:first-child,
        .calendar-table td:first-child {
            width: 100px;
            text-align: left;
            padding-left: 6px;
            font-weight: bold;
        }

        .calendar-table td {
            background-clip: padding-box;
        }

        .calendar-table th {
            font-size: 0.75rem;
        }

        .invalid-day {
            background: repeating-linear-gradient(45deg, #00d2f8, #00ffff 10px, #00ffff 10px, #00ffff 20px);
            opacity: 0.7;
        }

        .national-day {
            background: repeating-linear-gradient(45deg, #ba0000, #f90000 10px, #f90000 10px, #f90000 20px);
            opacity: 0.7;
        }
        
        .day-content {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            font-weight: bold;
            font-size: 0.7rem;
            color: white;
            text-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
        }
        
        .day-tooltip {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 100;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.2s;
        }
        
        td:hover .day-tooltip {
            visibility: visible;
            opacity: 1;
        }
        
        /* New styles for day numbers */
        .day-number {
            position: absolute;
            top: 1px;
            left: 1px;
            font-size: 8px;
            color: #666;
            font-weight: normal;
        }
        
        .event-icon {
            position: relative;
            z-index: 2;
        }
    </style>

    <div class="table-responsive">
        <table class="calendar-table table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th style="padding-left: 20px; font-size: 0.9rem;">Month</th>
                    @for ($day = 1; $day <= 31; $day++)
                        <th style="min-width: 28px; font-size: 0.9rem;">{{ $day }}</th>
                    @endfor
                </tr>
            </thead>

            <tbody>
                @foreach (range(1, 12) as $month)
                    {{-- AM Row --}}
                    <tr>
                        <td rowspan="2" class="text-start fw-semibold align-middle">
                            <div style="padding-left: 20px">
                                {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                            </div>
                        </td>

                        @for ($day = 1; $day <= 31; $day++)
                            @php
                                $date = sprintf('%d-%02d-%02d', $year, $month, $day);
                                $validDate = checkdate($month, $day, $year);
                                $amMatch = null;
                            @endphp

                            <td class="p-0">
                                <span class="day-number">{{ $day }}</span>
                                
                                @if (!$validDate)
                                    <div class="invalid-day" style="height: 28px;">
                                        <div class="day-content event-icon">I</div>
                                        <div class="day-tooltip">Invalid Date</div>
                                    </div>
                                @elseif (isset($holidays[$date]))
                                    <div class="national-day" style="height: 28px;">
                                        <div class="day-content event-icon">H</div>
                                        <div class="day-tooltip">{{ $holidays[$date] }}</div>
                                    </div>
                                @else
                                    @if ($validDate)
                                        @foreach ($leaveRequests as $request)
                                            @php
                                                $start = \Carbon\Carbon::parse($request->start_date);
                                                $end = \Carbon\Carbon::parse($request->end_date);
                                                $target = \Carbon\Carbon::parse($date);
                                                $status = ucfirst(strtolower($request->status ?? 'Accepted'));
                                                $color = $statusColors[$status]['color'] ?? '#ccc';
                                                $icon = $statusColors[$status]['icon'] ?? '';
                                                $reason = $request->reason ?? '';
                                            @endphp

                                            @if ($target->between($start, $end))
                                                @php
                                                    $am = false;
                                                    if ($target->isSameDay($start)) {
                                                        $am = in_array($request->start_time, ['morning', 'full']);
                                                    } elseif ($target->isSameDay($end)) {
                                                        $am = in_array($request->end_time, ['morning', 'full']);
                                                    } else {
                                                        $am = true;
                                                    }
                                                @endphp

                                                @if ($am)
                                                    @php
                                                        $amMatch = [
                                                            'status' => $status, 
                                                            'color' => $color, 
                                                            'icon' => $icon,
                                                            'reason' => $reason
                                                        ];
                                                    @endphp
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif

                                    @if ($amMatch)
                                        <div style="height: 28px; background-color: {{ $amMatch['color'] }};">
                                            <div class="day-content event-icon">{{ $amMatch['icon'] }}</div>
                                            <div class="day-tooltip">{{ $amMatch['status'] }}: {{ $amMatch['reason'] }}</div>
                                        </div>
                                    @else
                                        <div style="height: 28px; background-color: #f8f9fa;">
                                            <div class="day-content" style="color: transparent;">-</div>
                                        </div>
                                    @endif
                                @endif
                            </td>
                        @endfor
                    </tr>

                    {{-- PM Row --}}
                    <tr>
                        @for ($day = 1; $day <= 31; $day++)
                            @php
                                $date = sprintf('%d-%02d-%02d', $year, $month, $day);
                                $validDate = checkdate($month, $day, $year);
                                $pmMatch = null;
                            @endphp

                            <td class="p-0">
                                <span class="day-number">{{ $day }}</span>
                                
                                @if (!$validDate)
                                    <div class="invalid-day" style="height: 28px;">
                                        <div class="day-content event-icon">I</div>
                                        <div class="day-tooltip">Invalid Date</div>
                                    </div>
                                @elseif (isset($holidays[$date]))
                                    <div class="national-day" style="height: 28px;">
                                        <div class="day-content event-icon">H</div>
                                        <div class="day-tooltip">{{ $holidays[$date] }}</div>
                                    </div>
                                @else
                                    @if ($validDate)
                                        @foreach ($leaveRequests as $request)
                                            @php
                                                $start = \Carbon\Carbon::parse($request->start_date);
                                                $end = \Carbon\Carbon::parse($request->end_date);
                                                $target = \Carbon\Carbon::parse($date);
                                                $status = ucfirst(strtolower($request->status ?? 'Accepted'));
                                                $color = $statusColors[$status]['color'] ?? '#ccc';
                                                $icon = $statusColors[$status]['icon'] ?? '';
                                                $reason = $request->reason ?? '';
                                            @endphp

                                            @if ($target->between($start, $end))
                                                @php
                                                    $pm = false;
                                                    if ($target->isSameDay($start)) {
                                                        $pm = in_array($request->start_time, ['afternoon', 'full']);
                                                    } elseif ($target->isSameDay($end)) {
                                                        $pm = in_array($request->end_time, ['afternoon', 'full']);
                                                    } else {
                                                        $pm = true;
                                                    }
                                                @endphp

                                                @if ($pm)
                                                    @php
                                                        $pmMatch = [
                                                            'status' => $status, 
                                                            'color' => $color, 
                                                            'icon' => $icon,
                                                            'reason' => $reason
                                                        ];
                                                    @endphp
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif

                                    @if ($pmMatch)
                                        <div style="height: 28px; background-color: {{ $pmMatch['color'] }};">
                                            <div class="day-content event-icon">{{ $pmMatch['icon'] }}</div>
                                            <div class="day-tooltip">{{ $pmMatch['status'] }}: {{ $pmMatch['reason'] }}</div>
                                        </div>
                                    @else
                                        <div style="height: 28px; background-color: #f8f9fa;">
                                            <div class="day-content" style="color: transparent;">-</div>
                                        </div>
                                    @endif
                                @endif
                            </td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection