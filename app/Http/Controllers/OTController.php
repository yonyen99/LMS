<?php

namespace App\Http\Controllers;

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

class OTController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Employee|Manager|Admin']);
    }

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

    public function create()
    {
        $departments = Department::pluck('name', 'id');
        return view('over_time.create', compact('departments'));
    }

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

    public function edit($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        if ($overtime->user_id !== Auth::id()) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to edit this request.');
        }

        $departments = Department::pluck('name', 'id');
        return view('over_time.edit', compact('overtime', 'departments'));
    }

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


    public function show($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        if ($overtime->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['Manager', 'Admin'])) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to view this request.');
        }

        return view('over_time.show', compact('overtime'));
    }

    public function destroy($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        if ($overtime->user_id !== Auth::id()) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to delete this request.');
        }

        $overtime->delete();

        return redirect()->route('over-time.index')->with('success', 'Overtime request deleted successfully.');
    }

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
}
