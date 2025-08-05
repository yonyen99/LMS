<?php

namespace App\Http\Controllers;

use App\Mail\LeaveRequestSubmitted;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\NonWorkingDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\LeaveSummary;
use App\Models\User;
use App\Models\Department;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Exports\LeaveRequestExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Yasumi\Yasumi;
use Illuminate\Support\Facades\Http;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = LeaveRequest::with('leaveType')
            ->where('user_id', auth()->id());

        if ($request->filled('statuses')) {
            $statuses = array_map('strtolower', $request->statuses);
            $query->whereIn('status', $statuses);
        }

        if ($request->filled('show_request') && $request->show_request == 'mine') {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('type')) {
            $query->whereHas('leaveType', function ($q) use ($request) {
                $q->where('name', $request->type);
            });
        }

        $statusRequestOptions = [
            'Planned',
            'Accepted',
            'Requested',
            'Rejected',
            'Cancellation',
            'Canceled',
        ];
        if ($request->filled('status_request') && in_array($request->status_request, $statusRequestOptions)) {
            $query->where('status', $request->status_request);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('duration', 'like', "%{$search}%")
                    ->orWhere('start_date', 'like', "%{$search}%")
                    ->orWhere('end_date', 'like', "%{$search}%")
                    ->orWhere('start_time', 'like', "%{$search}%")
                    ->orWhere('end_time', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");

                $q->orWhereHas('leaveType', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });

                $q->orWhereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }
        $sortOrder = $request->input('sort_order', 'new');

        if ($sortOrder === 'new') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'asc');
        }

        $statusColors = [
            'Planned'      => ['text' => '#ffffff', 'bg' => '#A59F9F'],
            'Accepted'     => ['text' => '#ffffff', 'bg' => '#447F44'],
            'Requested'    => ['text' => '#ffffff', 'bg' => '#FC9A1D'],
            'Rejected'     => ['text' => '#ffffff', 'bg' => '#F80300'],
            'Cancellation' => ['text' => '#ffffff', 'bg' => '#F80300'],
            'Canceled'     => ['text' => '#ffffff', 'bg' => '#F80300'],
        ];

        $leaveTypes = LeaveType::orderBy('name')->pluck('name');
        $leaveRequests = $query->paginate(10);

        return view('leaveRequest.index', compact('leaveRequests', 'statusColors', 'leaveTypes', 'statusRequestOptions'));
    }

    public function create()
    {
        $leaveTypes = LeaveType::all();

        if ($leaveTypes->isEmpty()) {
            return redirect()->route('leave-requests.index')
                ->with('error', 'No leave types are available. Please contact the administrator.');
        }

        return view('leaveRequest.create', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'start_time' => 'required|in:morning,afternoon,full',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required|in:morning,afternoon,full',
            'duration' => 'required|numeric|min:0.5',
            'reason' => 'nullable|string',
            'status' => 'required|in:planned,requested',
        ]);

        $user = auth()->user();

        $leaveRequest = DB::transaction(function () use ($request, $user) {
            $summary = LeaveSummary::where('department_id', $user->department_id)
                ->where('leave_type_id', $request->leave_type_id)
                ->lockForUpdate()
                ->first();

            if (!$summary) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'leave_type_id' => 'Leave summary not found for your department.',
                ]);
            }

            $entitled = $summary->entitled;
            if ($user->hasRole('Manager')) {
                $entitled += 2;
            }

            $taken = \App\Models\LeaveRequest::where('user_id', $user->id)
                ->where('leave_type_id', $request->leave_type_id)
                ->where('status', 'Accepted')
                ->sum('duration');

            $requested = \App\Models\LeaveRequest::where('user_id', $user->id)
                ->where('leave_type_id', $request->leave_type_id)
                ->where('status', 'Requested')
                ->sum('duration');

            $planned = \App\Models\LeaveRequest::where('user_id', $user->id)
                ->where('status', 'Planned')
                ->sum('duration');

            $available = $entitled - ($taken + $requested);

            if ($available < $request->duration) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'duration' => "Not enough leave available. You can request up to $available days.",
                ]);
            }

            if ($request->status === 'requested') {
                $summary->requested += $request->duration;
            }

            $summary->available_actual = max($entitled - $summary->taken, 0);
            $summary->save();

            return LeaveRequest::create([
                'user_id' => $user->id,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $request->start_date,
                'start_time' => $request->start_time,
                'end_date' => $request->end_date,
                'end_time' => $request->end_time,
                'duration' => $request->duration,
                'reason' => $request->reason,
                'status' => $request->status,
                'requested_at' => now(),
                'last_changed_at' => now(),
            ]);
        });

        /**
         * Send email to Admin and Manager users when user submits a leave request.
         */
        $managersInSameDept = User::role('Manager')
            ->where('department_id', $user->department_id)
            ->pluck('email');

        $admins = User::role('Admin')->pluck('email');

        $adminEmails = $managersInSameDept->merge($admins)->unique()->toArray();

        if (!empty($adminEmails)) {
            Mail::to($adminEmails)->send(new LeaveRequestSubmitted($leaveRequest));
        }

        /**
         * Telegram Notification
         */
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if ($botToken && $chatId) {
            $message = "📢 *New Leave Request Submitted*\n\n"
                . "👤 *User:* {$user->name}\n"
                . "🏢 *Department:* {$user->department->name}\n"
                . "📅 *From:* {$request->start_date} ({$request->start_time})\n"
                . "📅 *To:* {$request->end_date} ({$request->end_time})\n"
                . "🕒 *Duration:* {$request->duration} day(s)\n"
                . "📝 *Reason:* " . ($request->reason ?: 'N/A') . "\n"
                . "🔖 *Status:* {$request->status}";

            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        }

        return redirect()->route('leave-requests.index')->with('success', 'Leave request submitted and sent to approvers.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        return view('leaveRequest.show', compact('leaveRequest'));
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $this->authorize('delete', $leaveRequest);
        $leaveRequest->delete();
        return redirect()->route('leave-requests.index')->with('success', 'Leave request deleted.');
    }

    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorize('cancel-request', $leaveRequest);
        $leaveRequest->update([
            'status' => 'Canceled',
            'last_changed_at' => now(),
        ]);
        return redirect()->route('leave-requests.index')->with('success', 'Leave request canceled successfully.');
    }


    public function history($id)
    {
        $changs = LeaveRequest::with(['user', 'leaveType', 'statusChanges.user'])->findOrFail($id);
        $latestStatusChange = $changs->statusChanges->sortByDesc('changed_at')->first();

        return view('leaveRequest.history', compact('changs', 'latestStatusChange'));
    }

    public function exportPDF(Request $request)
    {
        try {
            $this->authorize('export', \App\Models\LeaveRequest::class);

            $query = LeaveRequest::query()
                ->with(['leaveType', 'user']);

            if (!auth()->user()->hasRole('Admin')) {
                $query->where('user_id', auth()->id());
            }

            if ($request->filled('statuses')) {
                $query->whereIn('status', $request->input('statuses', []));
            }

            if ($request->filled('type')) {
                $query->whereHas('leaveType', function ($q) use ($request) {
                    $q->where('name', $request->input('type'));
                });
            }

            if ($request->filled('status_request')) {
                $query->where('status', $request->input('status_request'));
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('reason', 'like', "%{$search}%")
                        ->orWhereHas('leaveType', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $sortOrder = $request->input('sort_order', 'new');
            $query->orderBy('id', $sortOrder === 'new' ? 'desc' : 'asc');

            $leaveRequests = $query->get();

            $data = [
                'title' => 'Leave Requests Report - ' . (auth()->user()->hasRole('Admin') ? 'All Users' : auth()->user()->name),
                'generatedAt' => now()->format('F j, Y \a\t H:i'),
                'user' => auth()->user(),
                'leaveRequests' => $leaveRequests,
                'statusColors' => [
                    'Planned' => ['text' => '#ffffff', 'bg' => '#A59F9F'],
                    'Accepted' => ['text' => '#ffffff', 'bg' => '#447F44'],
                    'Requested' => ['text' => '#ffffff', 'bg' => '#FC9A1D'],
                    'Rejected' => ['text' => '#ffffff', 'bg' => '#F80300'],
                    'Cancellation' => ['text' => '#ffffff', 'bg' => '#F80300'],
                    'Canceled' => ['text' => '#ffffff', 'bg' => '#F80300'],
                ]
            ];

            $pdf = Pdf::loadView('leaveRequest.pdf', $data)
                ->setPaper('A4', 'landscape')
                ->setOptions([
                    'dpi' => 150,
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'defaultFont' => 'dejavu sans',
                    'tempDir' => storage_path('app/temp')
                ]);

            $filename = 'leave-requests-' . (auth()->user()->hasRole('Admin') ? 'all-users' : str_replace(' ', '-', auth()->user()->name)) . '-' . now()->format('Y-m-d') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $this->authorize('export', \App\Models\LeaveRequest::class);

            $filename = 'leave-requests-' . (auth()->user()->hasRole('Admin') ? 'all-users' : str_replace(' ', '-', auth()->user()->name)) . '-' . now()->format('Y-m-d') . '.xlsx';

            return Excel::download(new LeaveRequestExport($request), $filename);
        } catch (\Exception $e) {
            Log::error('Excel Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate Excel: ' . $e->getMessage());
        }
    }

    public function print(Request $request)
    {
        try {
            $this->authorize('export', \App\Models\LeaveRequest::class);

            $query = LeaveRequest::query()
                ->with(['leaveType', 'user']);

            if (!auth()->user()->hasRole('Admin')) {
                $query->where('user_id', auth()->id());
            }

            if ($request->filled('statuses')) {
                $query->whereIn('status', $request->input('statuses', []));
            }

            if ($request->filled('type')) {
                $query->whereHas('leaveType', function ($q) use ($request) {
                    $q->where('name', $request->input('type'));
                });
            }

            if ($request->filled('status_request')) {
                $query->where('status', $request->input('status_request'));
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('reason', 'like', "%{$search}%")
                        ->orWhereHas('leaveType', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $sortOrder = $request->input('sort_order', 'new');
            $query->orderBy('id', $sortOrder === 'new' ? 'desc' : 'asc');

            $leaveRequests = $query->get();

            $data = [
                'title' => 'Leave Requests Report - ' . (auth()->user()->hasRole('Admin') ? 'All Users' : auth()->user()->name),
                'generatedAt' => now()->format('F j, Y \a\t H:i'),
                'user' => auth()->user(),
                'leaveRequests' => $leaveRequests,
                'statusColors' => [
                    'Planned' => ['text' => '#ffffff', 'bg' => '#A59F9F'],
                    'Accepted' => ['text' => '#ffffff', 'bg' => '#447F44'],
                    'Requested' => ['text' => '#ffffff', 'bg' => '#FC9A1D'],
                    'Rejected' => ['text' => '#ffffff', 'bg' => '#F80300'],
                    'Cancellation' => ['text' => '#ffffff', 'bg' => '#F80300'],
                    'Canceled' => ['text' => '#ffffff', 'bg' => '#F80300'],
                ]
            ];

            return view('leaveRequest.print', $data);
        } catch (\Exception $e) {
            Log::error('Print Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate print view: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);

        if (strtolower($leave->status) === 'planned') {
            $leave->status = $request->input('status');
            $leave->save();
        }

        return redirect()->back()->with('success', 'Leave request status updated.');
    }

    // 1️⃣ My Calendar
    public function individual(Request $request)
    {
        $user = Auth::user();
        $leaveRequests = LeaveRequest::with('leaveType')->where('user_id', $user->id)->get();

        // Get non-working days based on user role
        $nonWorkingDaysQuery = NonWorkingDay::query();

        // Replace hasRole with direct role check (assuming 'role' column exists)
        if ($user->role !== 'Admin') {
            if ($user->role === 'Manager') {
                // Managers see their department's non-working days
                $nonWorkingDaysQuery->where('department_id', $user->department_id);
            } else {
                // Regular users see global non-working days (department_id = null)
                $nonWorkingDaysQuery->whereNull('department_id');
            }
        }

        $nonWorkingDays = $nonWorkingDaysQuery->get();
        $leaveTypes = LeaveType::all();

        return view('calendars.individual', compact('leaveRequests', 'leaveTypes', 'nonWorkingDays'));
    }



    protected function getCambodianHolidays($year)
    {
        $response = Http::get('https://calendarific.com/api/v2/holidays', [
            'api_key' => env('CALENDARIFIC_API_KEY'),
            'country' => 'KH',
            'year' => $year,
            'type' => 'national'
        ]);

        if ($response->failed()) {
            return []; // fallback if error
        }

        $data = $response->json();

        $holidays = [];

        foreach ($data['response']['holidays'] ?? [] as $holiday) {
            $date = $holiday['date']['iso']; // e.g. "2025-04-14"
            $name = $holiday['name'];
            $holidays[$date] = $name;
        }

        return $holidays;
    }
    
    // 2️⃣ Yearly Calendar (simplified, display all 12 months)
    public function yearly(Request $request)
    {
        $year = $request->input('year', now()->year);
        $user = auth()->user();

        $leaveRequests = LeaveRequest::where('user_id', $user->id)
            ->where(function ($query) use ($year) {
                $query->whereDate('start_date', '<=', "$year-12-31")
                    ->whereDate('end_date', '>=', "$year-01-01");
            })
            ->get();

        $holidays = $this->getCambodianHolidays($year);

        return view('calendars.yearly', compact('leaveRequests', 'year', 'holidays'));
    }


    // 3️⃣ My Workmates' Leave Calendar (for coworkers in same department)
    public function workmates(Request $request)
    {
        $user = auth()->user();

        // Admins see all users
        if ($user->hasRole('Admin')) { // or use $user->is_admin if using a boolean flag
            $workmates = User::all();
        } else {
            $workmates = User::where('department_id', $user->department_id)->get();
        }
        
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $currentDate = Carbon::create($year, $month, 1);

        // Always start on Sunday and end on Saturday
        $startDate = $currentDate->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $endDate = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        // Fetch leave requests more efficiently
        $leaveRequests = LeaveRequest::with('user')
            ->whereIn('user_id', $workmates->pluck('id'))
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<', $startDate)
                        ->where('end_date', '>', $endDate);
                });
            })
            ->get();

        $statusColors = [
            'Planned' => '#A59F9F',
            'Accepted' => '#447F44',
            'Requested' => '#FC9A1D',
            'Rejected' => '#F80300',
            'Cancellation' => '#F80300',
            'Canceled' => '#F80300',
        ];

        $leaveMap = [];

        foreach ($leaveRequests as $leave) {
            $start = Carbon::parse($leave->start_date)->startOfDay();
            $end = Carbon::parse($leave->end_date)->startOfDay();
            
            // Create period including both start and end dates
            $period = CarbonPeriod::create($start, $end);

            foreach ($period as $date) {
                $dateKey = $date->format('Y-m-d');
                $isFirstDay = $date->isSameDay($start);
                $isLastDay = $date->isSameDay($end);

                $isHalfDay = false;
                $halfDayType = null;

                // Half-day logic
                if ($isFirstDay && $isLastDay) {
                    // Single day leave
                    if ($leave->start_time === 'morning' && $leave->end_time === 'morning') {
                        $isHalfDay = true;
                        $halfDayType = 'AM';
                    } elseif ($leave->start_time === 'afternoon' && $leave->end_time === 'afternoon') {
                        $isHalfDay = true;
                        $halfDayType = 'PM';
                    }
                } else {
                    // Multi-day leave
                    if ($isFirstDay && $leave->start_time === 'afternoon') {
                        $isHalfDay = true;
                        $halfDayType = 'PM';
                    } elseif ($isLastDay && $leave->end_time === 'morning') {
                        $isHalfDay = true;
                        $halfDayType = 'AM';
                    }
                }

                $leaveMap[$leave->user->id][$dateKey] = [
                    'status' => $leave->status,
                    'name' => $leave->user->name,
                    'is_half_day' => $isHalfDay,
                    'half_day_type' => $halfDayType,
                ];
            }
        }

        // Build calendar weeks
        $weeks = [];
        $week = [];
        $date = $startDate->copy();

        while ($date <= $endDate) {
            $dayData = [
                'date' => $date->copy(),
                'is_current_month' => $date->month == $currentDate->month,
                'users' => [],
            ];

            foreach ($workmates as $workmate) {
                $dateKey = $date->format('Y-m-d');
                $leaveData = $leaveMap[$workmate->id][$dateKey] ?? null;

                $dayData['users'][] = $leaveData ?: [
                    'status' => null,
                    'name' => $workmate->name,
                    'is_half_day' => false,
                    'half_day_type' => null,
                ];
            }

            $week[] = $dayData;

            if ($date->dayOfWeek == Carbon::SATURDAY) {
                $weeks[] = $week;
                $week = [];
            }

            $date->addDay();
        }

        // Ensure we don't miss the last week if it doesn't end on Saturday
        if (!empty($week)) {
            $weeks[] = $week;
        }

        return view('calendars.workmates', [
            'weeks' => $weeks,
            'currentDate' => $currentDate,
            'workmates' => $workmates,
            'statusColors' => $statusColors, // Pass to view
        ]);
    }


    public function department(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $selectedDepartmentIds = (array) $request->input('departments', ['all']);

        $currentDate = Carbon::create($year, $month, 1);
        $monthName = $currentDate->format('F');
        $isToday = $currentDate->format('Y-m') === now()->format('Y-m');

        $startDate = $currentDate->copy()->startOfWeek(Carbon::SUNDAY);
        $dates = collect();
        for ($i = 0; $i < 42; $i++) {
            $dates->push($startDate->copy()->addDays($i));
        }

        $weeks = $dates->chunk(7);

        $departments = Department::all();
        $endDate = $startDate->copy()->addDays(41);

        $leaveRequestsQuery = LeaveRequest::with('user.department')
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate);

        if (!in_array('all', $selectedDepartmentIds) && count($selectedDepartmentIds) > 0) {
            $leaveRequestsQuery->whereHas('user.department', function ($query) use ($selectedDepartmentIds) {
                $query->whereIn('id', $selectedDepartmentIds);
            });
        }

        $leaveRequests = $leaveRequestsQuery->get();

        $events = [];
        foreach ($leaveRequests as $leave) {
            $period = CarbonPeriod::create($leave->start_date, $leave->end_date);
            foreach ($period as $date) {
                $dateStr = $date->toDateString();
                $status = ucfirst(strtolower($leave->status ?? 'Planned')); // Normalize case
                $events[$dateStr][] = [
                    'title' => $leave->user->name,
                    'status' => $status,
                ];
            }
        }

        $statusColors  = [
            'Planned' => '#A59F9F',
            'Accepted' => '#447F44',
            'Requested' => '#FC9A1D',
            'Rejected' => '#F80300',
            'Cancellation' => '#F80300',
            'Canceled' => '#F80300',
        ];

        return view('calendars.department', compact(
            'month', 'year', 'monthName', 'weeks', 'events',
            'currentDate', 'isToday', 'departments', 'selectedDepartmentIds', 'statusColors'
        ));
    }

}
