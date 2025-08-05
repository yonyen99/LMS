<?php

namespace App\Http\Controllers;

use App\Exports\OvertimeRequestsExport;
use App\Models\Department;
use App\Models\OvertimeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\OvertimeRequestSubmitted;
use App\Mail\OvertimeRequestStatusUpdated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class OTController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Employee|Manager|Admin']);
    }


    /**
     * Display a listing of the overtime requests
     * Users can see their own requests, Managers can see their department's requests,
     * and Admins can see all requests.
     */

    public function index()
    {
        $user = auth()->user();

        $query = OvertimeRequest::with(['user', 'department', 'actionBy']);

        if ($user->hasRole('Employee')) {
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('Manager')) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        } elseif ($user->hasRole('Admin')) {
            // Admins see all requests
        } else {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        $totalRequests = $query->count();
        $approvedRequests = (clone $query)->where('status', 'approved')->count();
        $pendingRequests = (clone $query)->where('status', 'requested')->count();

        $overtimes = $query->latest()->paginate(10);

        return view('over_time.list_over_time', compact('overtimes', 'totalRequests', 'approvedRequests', 'pendingRequests'));
    }

    /**
     * Display the overtime request form
     * Only users with a department can submit an overtime request.
     * If the user is not assigned to a department, they will receive a validation error.
     */

    public function overTime()
    {
        $user = auth()->user();

        $query = OvertimeRequest::with(['user', 'department', 'actionBy']);

        if ($user->hasRole('Employee')) {
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('Manager')) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        } elseif ($user->hasRole('Admin')) {
            // Admins see all requests
        } else {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        return view('over_time.over_time', [
            'overtimes' => $query->latest()->paginate(10),
            'totalRequests' => $query->count(),
            'approvedRequests' => $query->where('status', 'approved')->count(),
            'pendingRequests' => $query->where('status', 'requested')->count(),
        ]);
    }

    /**
     * Create a new overtime request
     * The user must be assigned to a department to submit an overtime request.
     * If the user is not assigned to a department, they will receive a validation error.
     */

    public function create()
    {
        $departments = Department::pluck('name', 'id');
        return view('over_time.create', compact('departments'));
    }

    /**
     * Store a new overtime request
     * The user must be assigned to a department to submit an overtime request.
     * If the user is not assigned to a department, they will receive a validation error.
     */

    public function store(Request $request)
    {
        $request->validate([
            'overtime_date'   => 'required|date',
            'time_period'     => 'required|in:before_shift,after_shift,weekend,holiday',
            'start_time'      => 'required|date_format:H:i',
            'end_time'        => 'required|date_format:H:i|after:start_time',
            'duration'        => 'required|numeric|min:0.5|max:24',
            'reason'          => 'required|string',
        ]);

        $user = auth()->user();

        // Check if the user has a department_id
        if (!$user->department_id) {
            throw ValidationException::withMessages([
                'department_id' => 'You must be assigned to a department to submit an overtime request.',
            ]);
        }

        $overtime = DB::transaction(function () use ($request, $user) {
            return OvertimeRequest::create([
                'overtime_date'   => $request->overtime_date,
                'time_period'     => $request->time_period,
                'start_time'      => $request->start_time,
                'end_time'        => $request->end_time,
                'duration'        => $request->duration,
                'reason'          => $request->reason,
                'status'          => 'requested',
                'requested_at'    => now(),
                'last_changed_at' => now(),
                'user_id'         => $user->id,
                'department_id'   => $user->department_id, // Add department_id
            ]);
        });

        try {
            $managersInSameDept = User::role('Manager')
                ->where('department_id', $user->department_id)
                ->pluck('email');
            $admins = User::role('Admin')->pluck('email');
            $adminEmails = $managersInSameDept->merge($admins)->unique()->toArray();

            if (!empty($adminEmails)) {
                Mail::to($adminEmails)->queue(new OvertimeRequestSubmitted($overtime));
            }

            $botToken = config('services.telegram.bot_token');
            $chatId = config('services.telegram.chat_id');
            if ($botToken && $chatId) {
                $departmentName = $user->department ? $user->department->name : 'N/A';
                $message = "📢 *New Overtime Request Submitted*\n\n"
                    . "👤 *User:* {$user->name}\n"
                    . "🏢 *Department:* {$departmentName}\n"
                    . "📅 *Date:* {$request->overtime_date}\n"
                    . "⏰ *Time Period:* " . ucwords(str_replace('_', ' ', $request->time_period)) . "\n"
                    . "🕒 *Start Time:* {$request->start_time}\n"
                    . "🕔 *End Time:* {$request->end_time}\n"
                    . "⏳ *Duration:* {$request->duration} hour(s)\n"
                    . "📝 *Reason:* {$request->reason}\n"
                    . "🔖 *Status:* {$overtime->status}";
                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown',
                ])->throw();
            }
        } catch (\Exception $e) {
            Log::error('Failed to send overtime notifications: ' . $e->getMessage());
        }

        return redirect()->route('over-time.index')->with('success', 'Overtime request submitted and sent to approvers.');
    }

    /**
     * Edit an overtime request
     * Only the user who created the request can edit it.
     * If the user is not authorized, they will be redirected with an error message.
     */

    public function edit($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        if ($overtime->user_id !== Auth::id()) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to edit this request.');
        }

        $departments = Department::pluck('name', 'id');
        return view('over_time.edit', compact('overtime', 'departments'));
    }

    /**
     * Update an overtime request
     * Only the user who created the request can update it.
     * If the user is not authorized, they will be redirected with an error message.
     */

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $overtime = OvertimeRequest::findOrFail($id);

        // Only allow the owner to update
        if ($overtime->user_id !== $user->id) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to edit this request.');
        }

        // Ensure the user has a department
        if (!$user->department_id) {
            throw ValidationException::withMessages([
                'department_id' => 'You must be assigned to a department to update an overtime request.',
            ]);
        }

        $request->validate([
            'overtime_date'  => 'required|date',
            'time_period'    => 'required|in:before_shift,after_shift,weekend,holiday',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i|after:start_time',
            'duration'       => 'required|numeric|min:0.5|max:24',
            'reason'         => 'required|string',
        ]);

        DB::transaction(function () use ($request, $user, $overtime) {
            $overtime->update([
                'overtime_date'   => $request->overtime_date,
                'time_period'     => $request->time_period,
                'start_time'      => $request->start_time,
                'end_time'        => $request->end_time,
                'duration'        => $request->duration,
                'reason'          => $request->reason,
                'department_id'   => $user->department_id,
                'last_changed_at' => now(),
            ]);
        });

        return redirect()->route('over-time.index')->with('success', 'Overtime request updated successfully.');
    }


    /**
     * Show an overtime request
     * Only the user who created the request, or an Admin/Manager in the same department
     * can view the request.
     */


    public function show($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        if ($overtime->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['Manager', 'Admin'])) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to view this request.');
        }

        return view('over_time.show', compact('overtime'));
    }

    /**
     * Delete an overtime request
     * Only the user who created the request can delete it.
     * If the user is not authorized, they will be redirected with an error message.
     */

    public function destroy($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        if ($overtime->user_id !== Auth::id()) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to delete this request.');
        }

        $overtime->delete();

        return redirect()->route('over-time.index')->with('success', 'Overtime request deleted successfully.');
    }

    /**
     * Accept an overtime request
     * Only an Admin or a Manager in the same department can accept the request.
     * If the user is not authorized, they will be redirected with an error message.
     */

    public function accept(Request $request, $id)
    {
        $user = auth()->user();
        $overtime = OvertimeRequest::findOrFail($id);

        if (!$user->hasRole('Admin') && !($user->hasRole('Manager') && $overtime->user->department_id === $user->department_id)) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to accept this request.');
        }

        DB::transaction(function () use ($overtime, $user) {
            $overtime->update([
                'status' => 'approved',
                'action_by' => $user->id,
                'last_changed_at' => now(),
            ]);

            Mail::to($overtime->user->email)->queue(new OvertimeRequestStatusUpdated($overtime));
        });

        return redirect()->route('over-time.index')->with('success', 'Overtime request approved successfully.');
    }

    /**
     * Reject an overtime request
     * Only an Admin or a Manager in the same department can reject the request.
     * If the user is not authorized, they will be redirected with an error message.
     */

    public function reject(Request $request, $id)
    {
        $user = auth()->user();
        $overtime = OvertimeRequest::findOrFail($id);

        if (!$user->hasRole('Admin') && !($user->hasRole('Manager') && $overtime->user->department_id === $user->department_id)) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to reject this request.');
        }

        DB::transaction(function () use ($overtime, $user) {
            $overtime->update([
                'status' => 'rejected',
                'action_by' => $user->id,
                'last_changed_at' => now(),
            ]);

            Mail::to($overtime->user->email)->queue(new OvertimeRequestStatusUpdated($overtime));
        });

        return redirect()->route('over-time.index')->with('success', 'Overtime request rejected successfully.');
    }

    /**
     * Cancel an overtime request
     * Only the user who created the request, or an Admin/Manager in the same department
     * can cancel the request.
     */

    public function cancel(Request $request, $id)
    {
        $user = auth()->user();
        $overtime = OvertimeRequest::findOrFail($id);

        if (
            !$user->hasRole('Admin') &&
            !($user->hasRole('Manager') && $overtime->user->department_id === $user->department_id) &&
            $user->id !== $overtime->user_id
        ) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to cancel this request.');
        }

        DB::transaction(function () use ($overtime, $user) {
            $overtime->update([
                'status' => 'canceled',
                'action_by' => $user->id,
                'last_changed_at' => now(),
            ]);

            if (auth()->id() !== $overtime->user_id) {
                Mail::to($overtime->user->email)->queue(new OvertimeRequestStatusUpdated($overtime));
            }
        });

        return redirect()->route('over-time.index')->with('success', 'Overtime request cancelled successfully.');
    }

    /**
     * Search overtime requests by date, by name,  and by department
     */

    public function search(Request $request)
    {
        $query = OvertimeRequest::with(['user', 'department', 'actionBy']);

        if ($request->filled('date')) {
            $query->whereDate('overtime_date', $request->date);
        }

        if ($request->filled('name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $overtimes = $query->latest()->paginate(10);

        return view('over_time.list_over_time', compact('overtimes'));
    }


    /**
     * Export overtime requests as PDF
     */

    public function exportPDF(Request $request)
    {
        $query = OvertimeRequest::with(['user', 'department', 'actionBy']);

        // Apply role-based filtering
        if (auth()->user()->hasRole('Employee')) {
            $query->where('user_id', auth()->id());
        } elseif (auth()->user()->hasRole('Manager')) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())
                    ->orWhereIn('user_id', function ($subQuery) {
                        $subQuery->select('id')
                            ->from('users')
                            ->where('department_id', auth()->user()->department_id);
                    });
            });
        }

        // Apply additional filters
        if ($request->filled('date')) {
            $query->whereDate('overtime_date', $request->date);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('department_id') && auth()->user()->hasRole('Admin')) {
            $query->where('department_id', $request->department_id);
        }

        $overtimes = $query->get();

        $pdf = Pdf::loadView('over_time.pdf', compact('overtimes'));
        $pdfFilePath = storage_path('app/overtime_requests.pdf');
        $pdf->save($pdfFilePath);

        return response()->download($pdfFilePath, 'overtime_requests.pdf');
    }

    /**
     * Export overtime requests as Excel
     */
    public function exportExcel(Request $request)
    {
        $query = OvertimeRequest::with(['user', 'department', 'actionBy']);

        // Apply role-based filtering
        if (auth()->user()->hasRole('Employee')) {
            $query->where('user_id', auth()->id());
        } elseif (auth()->user()->hasRole('Manager')) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())
                    ->orWhereIn('user_id', function ($subQuery) {
                        $subQuery->select('id')
                            ->from('users')
                            ->where('department_id', auth()->user()->department_id);
                    });
            });
        }

        // Apply additional filters
        if ($request->filled('date')) {
            $query->whereDate('overtime_date', $request->date);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('department_id') && auth()->user()->hasRole('Admin')) {
            $query->where('department_id', $request->department_id);
        }

        $overtimes = $query->get();

        return Excel::download(new OvertimeRequestsExport($overtimes), 'overtime_requests.xlsx');
    }
}
