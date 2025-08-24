@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 bg-white rounded-2xl shadow-sm">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
        <h2 class="text-2xl font-semibold text-gray-800 tracking-tight">
            Yearly Calendar <span class="text-gray-500 text-lg font-normal">({{ Auth::user()->name }})</span>
        </h2>
        <div class="flex items-center gap-3">
            <form method="GET" action="" class="flex items-center gap-2">
                <button type="submit" name="year" value="{{ ($year ?? now()->year) - 1 }}"
                    class="p-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:ring-2 focus:ring-blue-300 focus:outline-none transition-colors duration-200"
                    aria-label="Previous Year">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <select name="year" onchange="this.form.submit()"
                    class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300 shadow-sm"
                    aria-label="Select Year">
                    @for ($y = ($year ?? now()->year) - 5; $y <= ($year ?? now()->year) + 5; $y++)
                        <option value="{{ $y }}" {{ $y == ($year ?? now()->year) ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" name="year" value="{{ ($year ?? now()->year) + 1 }}"
                    class="p-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:ring-2 focus:ring-blue-300 focus:outline-none transition-colors duration-200"
                    aria-label="Next Year">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Legend Section -->
    <div class="flex flex-wrap gap-3 mb-8">
        <span class="flex items-center gap-2 px-3 py-1.5 bg-cyan-50 text-cyan-700 rounded-full text-sm font-medium shadow-sm">
            <span class="font-bold">I</span> Invalid Day
        </span>
        <span class="flex items-center gap-2 px-3 py-1.5 bg-red-50 text-red-700 rounded-full text-sm font-medium shadow-sm">
            <span class="font-bold">H</span> Holiday
        </span>
    </div>

    <!-- Calendar Styles -->
    <style>
        .calendar-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
            font-size: 0.875rem;
        }

        .calendar-table th,
        .calendar-table td {
            border: 1px solid #e5e7eb;
            text-align: center;
            vertical-align: middle;
            height: 60px;
            position: relative;
            transition: all 0.2s ease;
        }

        .calendar-table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #1e293b;
            padding: 0.75rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .calendar-table th:first-child,
        .calendar-table td:first-child {
            width: 120px;
            text-align: left;
            padding-left: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            background-color: #f8fafc;
        }

        .invalid-day {
            background: repeating-linear-gradient(
                45deg,
                #06b6d4,
                #06b6d4 4px,
                #22d3ee 4px,
                #22d3ee 8px
            );
            opacity: 0.95;
        }

        .holiday {
            background: repeating-linear-gradient(
                45deg,
                #b91c1c,
                #b91c1c 4px,
                #dc2626 4px,
                #dc2626 8px
            );
            opacity: 0.95;
        }

        .day-content {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            font-weight: 600;
            color: white;
            font-size: 1rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .day-number {
            position: absolute;
            top: 6px;
            left: 6px;
            font-size: 0.7rem;
            color: #6b7280;
            font-weight: 500;
        }

        .day-tooltip {
            position: absolute;
            bottom: calc(100% + 12px);
            left: 50%;
            transform: translateX(-50%);
            background: #1e293b;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 40;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        td:hover .day-tooltip {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(-8px);
        }

        .leave-day {
            position: relative;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        td:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 20;
        }

        .calendar-table td:focus {
            outline: 2px solid #3b82f6;
            outline-offset: -2px;
            z-index: 20;
        }

        .calendar-table tr:hover td:first-child {
            background-color: #f1f5f9;
        }

        .bg-gray-50:hover {
            background-color: #e5e7eb;
        }

        /* Accessibility Enhancements */
        .calendar-table td:focus-visible {
            outline: 2px solid #3b82f6;
            outline-offset: -2px;
        }

        /* Smooth Scroll for Mobile */
        .overflow-x-auto {
            scroll-behavior: smooth;
        }

        /* Border Radius for Table Edges */
        .calendar-table th:first-child {
            border-top-left-radius: 0.5rem;
        }

        .calendar-table th:last-child {
            border-top-right-radius: 0.5rem;
        }

        .calendar-table tr:last-child td:first-child {
            border-bottom-left-radius: 0.5rem;
        }

        .calendar-table tr:last-child td:last-child {
            border-bottom-right-radius: 0.5rem;
        }
    </style>

    <!-- Calendar Table -->
    <div class="overflow-x-auto rounded-xl shadow-sm">
        <table class="calendar-table" role="grid" aria-label="Yearly Calendar for {{ $year ?? now()->year }}">
            <thead>
                <tr>
                    <th scope="col" class="text-left">Month</th>
                    @for ($day = 1; $day <= 31; $day++)
                        <th scope="col">{{ $day }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach (range(1, 12) as $month)
                    <tr>
                        <td class="text-left" scope="row">
                            {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                        </td>
                        @for ($day = 1; $day <= 31; $day++)
                            @php
                                $date = sprintf('%d-%02d-%02d', $year ?? now()->year, $month, $day);
                                $validDate = checkdate($month, $day, $year ?? now()->year);
                                $match = null;
                                if ($validDate) {
                                    foreach ($leaveRequests as $request) {
                                        $start = \Carbon\Carbon::parse($request->start_date);
                                        $end = \Carbon\Carbon::parse($request->end_date);
                                        $target = \Carbon\Carbon::parse($date);
                                        if ($target->between($start, $end)) {
                                            $status = ucfirst(strtolower($request->status ?? 'Accepted'));
                                            $match = [
                                                'status' => $status,
                                                'color' => $statusColors[$status]['color'] ?? '#d1d5db',
                                                'icon' => $statusColors[$status]['icon'] ?? '',
                                                'reason' => $request->reason ?? ''
                                            ];
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <td class="relative" tabindex="0" role="gridcell"
                                aria-label="Day {{ $day }} of {{ DateTime::createFromFormat('!m', $month)->format('F') }} in {{ $year ?? now()->year }}">
                                <span class="day-number">{{ $day }}</span>
                                @if (!$validDate)
                                    <div class="invalid-day h-full">
                                        <div class="day-content">I</div>
                                        <div class="day-tooltip">Invalid Date</div>
                                    </div>
                                @elseif (isset($holidays[$date]))
                                    <div class="holiday h-full">
                                        <div class="day-content">H</div>
                                        <div class="day-tooltip">{{ $holidays[$date] }}</div>
                                    </div>
                                @elseif ($match)
                                    <div class="leave-day h-full" style="background-color: {{ $match['color'] }};">
                                        <div class="day-content">{{ $match['icon'] }}</div>
                                        <div class="day-tooltip">{{ $match['status'] }}: {{ $match['reason'] }}</div>
                                    </div>
                                @else
                                    <div class="h-full bg-gray-50">
                                        <div class="day-content text-transparent">-</div>
                                    </div>
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