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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Exports\LeaveRequestExport;
use Maatwebsite\Excel\Facades\Excel;
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

    public function calendar()
    {
        $user = Auth::user();
        $leaveRequests = LeaveRequest::with('leaveType')->where('user_id', $user->id)->get();

        $nonWorkingDaysQuery = NonWorkingDay::query();

        if ($user->hasRole('Admin')) {
            // Admins can see all non-working days
        } elseif ($user->hasRole('Manager')) {
            $nonWorkingDaysQuery->where('department_id', $user->department_id);
        } else {
            $nonWorkingDaysQuery->whereNull('department_id');
        }

        $nonWorkingDays = $nonWorkingDaysQuery->get();
        $leaveTypes = LeaveType::all();

        return view('leaveRequest.calendar', compact('leaveRequests', 'leaveTypes', 'nonWorkingDays'));
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
}
