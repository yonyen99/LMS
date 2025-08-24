@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 bg-white rounded-lg shadow-sm">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-3">
        <h2 class="text-lg font-medium text-gray-900">
            {{ $year ?? now()->year }} Calendar <span class="text-gray-500 text-sm font-normal">({{ Auth::user()->name }})</span>
        </h2>
        <form method="GET" action="" class="flex items-center gap-1.5">
            <button type="submit" name="year" value="{{ ($year ?? now()->year) - 1 }}"
                class="p-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 focus:outline-none transition-colors duration-100"
                aria-label="Previous Year">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <select name="year" onchange="this.form.submit()"
                class="px-2 py-1.5 bg-white border border-gray-200 rounded-md text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-colors duration-100"
                aria-label="Select Year">
                @for ($y = ($year ?? now()->year) - 5; $y <= ($year ?? now()->year) + 5; $y++)
                    <option value="{{ $y }}" {{ $y == ($year ?? now()->year) ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" name="year" value="{{ ($year ?? now()->year) + 1 }}"
                class="p-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 focus:outline-none transition-colors duration-100"
                aria-label="Next Year">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </form>
    </div>

    <!-- Legend Section -->
    <div class="flex flex-wrap gap-2 mb-6">
        <span class="flex items-center gap-1.5 px-2.5 py-1 bg-teal-50 text-teal-800 rounded-md text-xs font-medium transition-colors duration-100 hover:bg-teal-100 focus:bg-teal-100 focus:ring-2 focus:ring-teal-500 focus:ring-offset-1 focus:outline-none">
            <span class="w-3 h-3 rounded-full bg-teal-500"></span> Invalid Day
        </span>
        <span class="flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 text-rose-800 rounded-md text-xs font-medium transition-colors duration-100 hover:bg-rose-100 focus:bg-rose-100 focus:ring-2 focus:ring-rose-500 focus:ring-offset-1 focus:outline-none">
            <span class="w-3 h-3 rounded-full bg-rose-500"></span> Holiday
        </span>
    </div>

    <!-- Calendar Styles -->
    <style>
        :root {
            --primary-color: #4f46e5; /* Indigo */
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --bg-light: #f9fafb;
            --border-light: #e5e7eb;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --transition: all 0.15s ease;
        }

        .calendar-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
            font-size: 0.85rem;
            background-color: var(--bg-light);
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .calendar-table th,
        .calendar-table td {
            border: 1px solid var(--border-light);
            text-align: center;
            vertical-align: middle;
            height: 44px;
            position: relative;
            transition: var(--transition);
        }

        .calendar-table th {
            background-color: var(--bg-light);
            font-weight: 500;
            color: var(--text-primary);
            padding: 0.5rem;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.04em;
        }

        .calendar-table th:first-child,
        .calendar-table td:first-child {
            width: 90px;
            text-align: left;
            padding-left: 0.75rem;
            font-weight: 500;
            color: var(--text-primary);
            background-color: var(--bg-light);
        }

        .invalid-day {
            background: repeating-linear-gradient(
                45deg,
                #14b8a6,
                #14b8a6 4px,
                #2dd4bf 4px,
                #2dd4bf 8px
            );
            opacity: 0.85;
        }

        .holiday {
            background: repeating-linear-gradient(
                45deg,
                #e11d48,
                #e11d48 4px,
                #fb7185 4px,
                #fb7185 8px
            );
            opacity: 0.85;
        }

        .day-content {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            font-weight: 500;
            color: white;
            font-size: 0.8rem;
        }

        .day-number {
            position: absolute;
            top: 3px;
            left: 3px;
            font-size: 0.6rem;
            color: var(--text-secondary);
            font-weight: 400;
        }

        .day-tooltip {
            position: absolute;
            bottom: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%);
            background: var(--text-primary);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.7rem;
            white-space: nowrap;
            z-index: 40;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.15s ease, transform 0.15s ease;
            box-shadow: var(--shadow-sm);
        }

        td:hover .day-tooltip {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(-2px);
        }

        .leave-day {
            position: relative;
            transition: var(--transition);
        }

        td:hover {
            transform: scale(1.02);
            box-shadow: var(--shadow-sm);
            z-index: 20;
        }

        .calendar-table td:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: -1px;
            z-index: 20;
        }

        .calendar-table tr:hover td:first-child {
            background-color: #f3f4f6;
        }

        .bg-gray-50:hover {
            background-color: #e5e7eb;
        }

        /* Accessibility Enhancements */
        .calendar-table td:focus-visible {
            outline: 2px solid var(--primary-color);
            outline-offset: -1px;
        }

        /* Smooth Scroll for Mobile */
        .overflow-x-auto {
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
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

        /* Responsive Adjustments */
        @media (max-width: 640px) {
            .calendar-table {
                font-size: 0.7rem;
            }

            .calendar-table th,
            .calendar-table td {
                height: 36px;
            }

            .calendar-table th:first-child,
            .calendar-table td:first-child {
                width: 70px;
                padding-left: 0.5rem;
            }

            .day-number {
                font-size: 0.55rem;
            }

            .day-content {
                font-size: 0.7rem;
            }

            .day-tooltip {
                font-size: 0.65rem;
                padding: 0.2rem 0.4rem;
            }
        }
    </style>

    <!-- Calendar Table -->
    <div class="overflow-x-auto rounded-lg">
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