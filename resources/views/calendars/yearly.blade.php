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
            background: repeating-linear-gradient(
                45deg,
                #00d2f8,
                #00ffff 10px,
                #00ffff 10px,
                #00ffff 20px
            );
            opacity: 0.7;
        }

        .national-day {
            background: repeating-linear-gradient(
                45deg,
                #ba0000,
                #f90000 10px,
                #f90000 10px,
                #f90000 20px
            );
            opacity: 0.7;
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

                            @if ($validDate)
                                @foreach ($leaveRequests as $request)
                                    @php
                                        $start = \Carbon\Carbon::parse($request->start_date);
                                        $end = \Carbon\Carbon::parse($request->end_date);
                                        $target = \Carbon\Carbon::parse($date);
                                        $status = ucfirst(strtolower($request->status ?? 'Accepted'));
                                        $color = $statusColors[$status] ?? '#ccc';
                                        $reason = $request->reason ?? '';
                                    @endphp

                                    @if ($target->between($start, $end))
                                        @if (
                                            ($request->start_time === 'morning' && $target->isSameDay($start)) ||
                                            ($request->end_time === 'morning' && $target->isSameDay($end)) ||
                                            ($request->start_time === 'morning' && $request->end_time === 'afternoon')
                                        )
                                            @php
                                                $amMatch = ['status' => $status, 'color' => $color, 'reason' => $reason];
                                            @endphp
                                        @endif
                                    @endif
                                @endforeach
                            @endif

                            <td class="p-0">
                                @if (!$validDate)
                                    <div class="invalid-day" style="height: 28px;"></div>
                                @elseif (isset($holidays[$date]))
                                    <div class="national-day" style="height: 28px;" title="{{ $holidays[$date] }}"></div>
                                @elseif ($amMatch)
                                    <div title="{{ $amMatch['status'] }}: {{ $amMatch['reason'] }}"
                                        style="height: 28px; background-color: {{ $amMatch['color'] }};"></div>
                                @else
                                    <div style="height: 28px; background-color: #f8f9fa;"></div>
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

                            @if ($validDate)
                                @foreach ($leaveRequests as $request)
                                    @php
                                        $start = \Carbon\Carbon::parse($request->start_date);
                                        $end = \Carbon\Carbon::parse($request->end_date);
                                        $target = \Carbon\Carbon::parse($date);
                                        $status = ucfirst(strtolower($request->status ?? 'Accepted'));
                                        $color = $statusColors[$status] ?? '#ccc';
                                        $reason = $request->reason ?? '';
                                    @endphp

                                    @if ($target->between($start, $end))
                                        @if (
                                            ($request->start_time === 'afternoon' && $target->isSameDay($start)) ||
                                            ($request->end_time === 'afternoon' && $target->isSameDay($end)) ||
                                            ($request->start_time === 'morning' && $request->end_time === 'afternoon')
                                        )
                                            @php
                                                $pmMatch = ['status' => $status, 'color' => $color, 'reason' => $reason];
                                            @endphp
                                        @endif
                                    @endif
                                @endforeach
                            @endif

                            <td class="p-0">
                                @if (!$validDate)
                                    <div class="invalid-day" style="height: 28px;"></div>
                                @elseif (isset($holidays[$date]))
                                    <div class="national-day" style="height: 28px;" title="{{ $holidays[$date] }}"></div>
                                @elseif ($pmMatch)
                                    <div title="{{ $pmMatch['status'] }}: {{ $pmMatch['reason'] }}"
                                        style="height: 28px; background-color: {{ $pmMatch['color'] }};"></div>
                                @else
                                    <div style="height: 28px; background-color: #f8f9fa;"></div>
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
