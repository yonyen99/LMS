<?php

namespace App\Http\Controllers;

use App\Mail\LeaveRequestSubmitted;
use App\Mail\LeaveRequestAccepted;
use App\Mail\DepartmentLeaveNotification;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveSummary;
use App\Models\User;
use App\Models\Department;
use App\Models\NonWorkingDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Http;
use App\Exports\LeaveRequestExport;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the leave requests for the authenticated user
     * with filtering and sorting capabilities
     */
    public function index(Request $request): View
    {
        // Base query for user's leave requests
        $query = LeaveRequest::with('leaveType')
            ->where('user_id', auth()->id());

        // Filter by statuses if provided
        if ($request->filled('statuses')) {
            $statuses = array_map('strtolower', $request->statuses);
            $query->whereIn('status', $statuses);
        }

        // Filter to show only current user's requests
        if ($request->filled('show_request') && $request->show_request == 'mine') {
            $query->where('user_id', auth()->id());
        }

        // Filter by leave type
        if ($request->filled('type')) {
            $query->whereHas('leaveType', function ($q) use ($request) {
                $q->where('name', $request->type);
            });
        }

        // Filter by specific status request
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

        // Search functionality across multiple fields
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('duration', 'like', "%{$search}%")
                    ->orWhere('start_date', 'like', "%{$search}%")
                    ->orWhere('end_date', 'like', "%{$search}%")
                    ->orWhere('start_time', 'like', "%{$search}%")
                    ->orWhere('end_time', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('leaveType', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Sort order (newest first by default)
        $sortOrder = $request->input('sort_order', 'new');
        $query->orderBy('id', $sortOrder === 'new' ? 'desc' : 'asc');

        // Status color mapping for UI display
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

    /**
     * Show the form for creating a new leave request
     */
    public function create()
    {
        $leaveTypes = LeaveType::all();

        // Check if leave types are available
        if ($leaveTypes->isEmpty()) {
            return redirect()->route('leave-requests.index')
                ->with('error', 'No leave types are available. Please contact the administrator.');
        }

        return view('leaveRequest.create', compact('leaveTypes'));
    }

    /**
     * Store a newly created leave request in storage
     * with validation, availability check, and notifications
     */

    public function store(Request $request)
    {
        // Validate input
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

        // No leave balance checks — just create the leave request
        $leaveRequest = LeaveRequest::create([
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

        // Send email notifications to managers and admins
        $managersInDept = User::role('Manager')
            ->where('department_id', $user->department_id)
            ->where('id', '!=', $user->id) // exclude the requester if Manager
            ->pluck('email');

        $admins = User::role('Admin')->pluck('email');

        $recipients = $managersInDept->merge($admins)->unique()->toArray();

        if (!empty($recipients)) {
            Mail::to($recipients)->send(new LeaveRequestSubmitted($leaveRequest));
        }

        // ✅ Send Telegram notification if configured
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

        return redirect()->route('leave-requests.index')
            ->with('success', "Leave request submitted successfully ({$leaveRequest->duration} day(s)).");
    }


    /**
     * Approve a leave request and send notifications
     * to the employee and department members
     */
    public function acceptRequest(LeaveRequest $leaveRequest)
    {
        // Authorization check
        $this->authorize('accept', $leaveRequest);

        $approver = auth()->user();

        Log::info('Leave approval initiated', [
            'leave_request_id' => $leaveRequest->id,
            'approver_id' => $approver->id,
            'approver_email' => $approver->email,
        ]);

        try {
            // Process approval in transaction
            DB::transaction(function () use ($leaveRequest, $approver) {
                // Update leave request status
                $leaveRequest->update([
                    'status' => 'Accepted',
                    'approved_by' => $approver->id,
                    'last_changed_at' => now()
                ]);

                // Update leave summary
                $summary = LeaveSummary::where('department_id', $leaveRequest->user->department_id)
                    ->where('leave_type_id', $leaveRequest->leave_type_id)
                    ->lockForUpdate()
                    ->first();

                if ($summary) {
                    $summary->taken += $leaveRequest->duration;
                    $summary->available_actual = max($summary->entitled - $summary->taken, 0);
                    $summary->save();
                }
            });

            // Reload relationships
            $leaveRequest->load(['user.department.users', 'leaveType']);
            $employee = $leaveRequest->user;

            // Check if employee email exists
            if (empty($employee->email)) {
                Log::error('Employee email missing', [
                    'leave_request_id' => $leaveRequest->id,
                    'employee_id' => $employee->id
                ]);
                return redirect()->route('leave-requests.index')
                    ->with('warning', 'Leave approved but employee email missing.');
            }

            // Send notification to the employee
            Mail::to($employee->email)
                ->queue(new LeaveRequestAccepted($leaveRequest, $approver->name));

            Log::info('Employee notification sent', [
                'leave_request_id' => $leaveRequest->id,
                'employee_email' => $employee->email
            ]);

            // Send notifications to all department members
            if ($employee->department_id) {
                $departmentMembers = $employee->department->users()
                    ->where('is_active', true)
                    ->whereNotNull('email')
                    ->get();

                if ($departmentMembers->isNotEmpty()) {
                    foreach ($departmentMembers as $member) {
                        try {
                            // Skip the employee (already notified)
                            if ($member->id === $employee->id) {
                                continue;
                            }

                            Mail::to($member->email)
                                ->queue(new DepartmentLeaveNotification($leaveRequest, $approver->name));

                            Log::debug('Department notification queued', [
                                'leave_request_id' => $leaveRequest->id,
                                'recipient_id' => $member->id,
                                'recipient_email' => $member->email
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Failed to queue department notification', [
                                'leave_request_id' => $leaveRequest->id,
                                'recipient_id' => $member->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }

            // Send Telegram notification
            $botToken = config('services.telegram.bot_token');
            $chatId = config('services.telegram.chat_id');

            if ($botToken && $chatId) {
                $message = "✅ *Leave Request Approved*\n\n"
                    . "👤 *Employee:* {$employee->name}\n"
                    . "🏢 *Department:* {$employee->department->name}\n"
                    . "👨‍💼 *Approved By:* {$approver->name}\n"
                    . "📅 *Dates:* {$leaveRequest->start_date->format('M d')} - {$leaveRequest->end_date->format('M d')}\n"
                    . "🕒 *Duration:* {$leaveRequest->duration} day(s)";

                try {
                    Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $message,
                        'parse_mode' => 'Markdown',
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send Telegram notification', [
                        'leave_request_id' => $leaveRequest->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return redirect()->route('leave-requests.index')
                ->with('success', 'Leave approved and notifications sent to department.');
        } catch (\Exception $e) {
            Log::error('Approval process failed', [
                'leave_request_id' => $leaveRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Approval process failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified leave request
     */
    public function show(LeaveRequest $leaveRequest)
    {
        return view('leaveRequest.show', compact('leaveRequest'));
    }

    /**
     * Remove the specified leave request from storage
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        $this->authorize('delete', $leaveRequest);
        $leaveRequest->delete();
        return redirect()->route('leave-requests.index')->with('success', 'Leave request deleted.');
    }

    /**
     * Cancel a leave request
     */
    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorize('cancel-request', $leaveRequest);
        $leaveRequest->update([
            'status' => 'Canceled',
            'last_changed_at' => now(),
        ]);
        return redirect()->route('leave-requests.index')->with('success', 'Leave request canceled successfully.');
    }

    /**
     * Display the history of status changes for a leave request
     */
    public function history($id)
    {
        $changs = LeaveRequest::with(['user', 'leaveType', 'statusChanges.user'])->findOrFail($id);
        $latestStatusChange = $changs->statusChanges->sortByDesc('changed_at')->first();

        return view('leaveRequest.history', compact('changs', 'latestStatusChange'));
    }

    /**
     * Export leave requests as PDF with filtering options
     */
    public function exportPDF(Request $request)
    {
        try {
            $this->authorize('export', LeaveRequest::class);

            $query = LeaveRequest::query()
                ->with(['leaveType', 'user']);

            // Restrict to user's own requests if not admin
            if (!auth()->user()->hasRole('Admin')) {
                $query->where('user_id', auth()->id());
            }

            // Apply filters
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

            // Apply sorting
            $sortOrder = $request->input('sort_order', 'new');
            $query->orderBy('id', $sortOrder === 'new' ? 'desc' : 'asc');

            $leaveRequests = $query->get();

            // Prepare data for PDF
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

            // Generate PDF
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

    /**
     * Export leave requests as Excel file
     */
    public function exportExcel(Request $request)
    {
        try {
            $this->authorize('export', LeaveRequest::class);

            $filename = 'leave-requests-' . (auth()->user()->hasRole('Admin') ? 'all-users' : str_replace(' ', '-', auth()->user()->name)) . '-' . now()->format('Y-m-d') . '.xlsx';

            return Excel::download(new LeaveRequestExport($request), $filename);
        } catch (\Exception $e) {
            Log::error('Excel Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate Excel: ' . $e->getMessage());
        }
    }

    /**
     * Generate a printable view of leave requests
     */
    public function print(Request $request)
    {
        try {
            $this->authorize('export', LeaveRequest::class);

            $query = LeaveRequest::query()
                ->with(['leaveType', 'user']);

            // Restrict to user's own requests if not admin
            if (!auth()->user()->hasRole('Admin')) {
                $query->where('user_id', auth()->id());
            }

            // Apply filters
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

            // Apply sorting
            $sortOrder = $request->input('sort_order', 'new');
            $query->orderBy('id', $sortOrder === 'new' ? 'desc' : 'asc');

            $leaveRequests = $query->get();

            // Prepare data for print view
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

    /**
     * Update the status of a planned leave request
     */
    public function updateStatus(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);

        // Only allow status update for planned requests
        if (strtolower($leave->status) === 'planned') {
            $leave->status = $request->input('status');
            $leave->save();
        }

        return redirect()->back()->with('success', 'Leave request status updated.');
    }

    /**
     * Display individual calendar view with user's leave requests
     * and non-working days based on user role
     */
    public function individual(Request $request)
    {
        $user = Auth::user();
        $leaveRequests = LeaveRequest::with('leaveType')->where('user_id', $user->id)->get();

        // Get non-working days based on user role
        $nonWorkingDaysQuery = NonWorkingDay::query();

        // Filter non-working days based on user role
        if ($user->role !== 'Admin') {
            if ($user->role === 'Manager') {
                // Managers see their department's non-working days
                $nonWorkingDaysQuery->where('department_id', $user->department_id);
            } else {
                // Regular users see global non-working days
                $nonWorkingDaysQuery->whereNull('department_id');
            }
        }

        $nonWorkingDays = $nonWorkingDaysQuery->get();
        $leaveTypes = LeaveType::all();

        return view('calendars.individual', compact('leaveRequests', 'leaveTypes', 'nonWorkingDays'));
    }

    /**
     * Get Cambodian holidays from Calendarific API
     */
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

    /**
     * Display yearly calendar view with user's leave requests and holidays
     */
    public function yearly(Request $request)
    {
        $year = $request->input('year', now()->year);
        $user = auth()->user();

        // Get leave requests for the specified year
        $leaveRequests = LeaveRequest::where('user_id', $user->id)
            ->where(function ($query) use ($year) {
                $query->whereDate('start_date', '<=', "$year-12-31")
                    ->whereDate('end_date', '>=', "$year-01-01");
            })
            ->get();

        // Get Cambodian holidays for the year
        $holidays = $this->getCambodianHolidays($year);

        return view('calendars.yearly', compact('leaveRequests', 'year', 'holidays'));
    }

    /**
     * Display workmates' leave calendar for users in the same department
     */
    public function workmates(Request $request)
    {
        $user = auth()->user();

        // Get workmates based on user role
        if ($user->hasRole('Admin')) {
            $workmates = User::all();
        } else {
            $workmates = User::where('department_id', $user->department_id)->get();
        }

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $currentDate = Carbon::create($year, $month, 1);

        // Set calendar boundaries (Sunday to Saturday)
        $startDate = $currentDate->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $endDate = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        // Fetch leave requests for workmates in the date range
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

        // Status color mapping
        $statusColors = [
            'Planned' => '#A59F9F',
            'Accepted' => '#447F44',
            'Requested' => '#FC9A1D',
            'Rejected' => '#F80300',
            'Cancellation' => '#F80300',
            'Canceled' => '#F80300',
        ];

        $leaveMap = [];

        // Map leave requests to dates for each user
        foreach ($leaveRequests as $leave) {
            $start = Carbon::parse($leave->start_date)->startOfDay();
            $end = Carbon::parse($leave->end_date)->startOfDay();

            $period = CarbonPeriod::create($start, $end);

            foreach ($period as $date) {
                $dateKey = $date->format('Y-m-d');
                $isFirstDay = $date->isSameDay($start);
                $isLastDay = $date->isSameDay($end);

                $isHalfDay = false;
                $halfDayType = null;

                // Determine half-day status
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

            // Add leave data for each workmate for this date
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

            // End of week (Saturday)
            if ($date->dayOfWeek == Carbon::SATURDAY) {
                $weeks[] = $week;
                $week = [];
            }

            $date->addDay();
        }

        // Add remaining days if any
        if (!empty($week)) {
            $weeks[] = $week;
        }

        return view('calendars.workmates', [
            'weeks' => $weeks,
            'currentDate' => $currentDate,
            'workmates' => $workmates,
            'statusColors' => $statusColors,
        ]);
    }

    /**
     * Display department calendar with leave requests filtered by department
     */
    public function department(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $selectedDepartmentIds = (array) $request->input('departments', ['all']);

        $currentDate = Carbon::create($year, $month, 1);
        $monthName = $currentDate->format('F');
        $isToday = $currentDate->format('Y-m') === now()->format('Y-m');

        // Start on Sunday
        $startDate = $currentDate->copy()->startOfWeek(Carbon::SUNDAY);

        // Generate 6 weeks of dates (42 days)
        $dates = collect();
        for ($i = 0; $i < 42; $i++) {
            $dates->push($startDate->copy()->addDays($i));
        }
        $weeks = $dates->chunk(7);

        $departments = Department::all();
        $endDate = $startDate->copy()->addDays(41);

        // Load leave requests with department and delegation info
        $leaveRequestsQuery = LeaveRequest::with(['user.department', 'user.delegation'])
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate);

        // Filter by selected departments
        if (!in_array('all', $selectedDepartmentIds) && count($selectedDepartmentIds) > 0) {
            $leaveRequestsQuery->whereHas('user.department', function ($query) use ($selectedDepartmentIds) {
                $query->whereIn('id', $selectedDepartmentIds);
            });
        }

        $leaveRequests = $leaveRequestsQuery->get();

        // Build events array for calendar display
        $events = [];
        foreach ($leaveRequests as $leave) {
            $period = CarbonPeriod::create($leave->start_date, $leave->end_date);
            foreach ($period as $date) {
                $dateStr = $date->toDateString();
                $status = ucfirst(strtolower($leave->status ?? 'Planned'));
                $events[$dateStr][] = [
                    'title'      => $leave->user->name,
                    'status'     => $status,
                    'delegation' => $leave->user->delegation->name ?? null,
                ];
            }
        }

        // Status color mapping
        $statusColors  = [
            'Planned'      => '#A59F9F',
            'Accepted'     => '#447F44',
            'Requested'    => '#FC9A1D',
            'Rejected'     => '#F80300',
            'Cancellation' => '#F80300',
            'Canceled'     => '#F80300',
        ];

        return view('calendars.department', compact(
            'month',
            'year',
            'monthName',
            'weeks',
            'events',
            'currentDate',
            'isToday',
            'departments',
            'selectedDepartmentIds',
            'statusColors'
        ));
    }
}
