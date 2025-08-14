<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveRequest;
    public $approverName;

    public function __construct(LeaveRequest $leaveRequest, string $approverName)
    {
        $this->leaveRequest = $leaveRequest;
        $this->approverName = $approverName;
    }

    public function build()
    {
        return $this->subject("Leave Request Approved")
            ->view('emails.leave_approved')
            ->with([
                'employeeName' => $this->leaveRequest->user->name,
                'leaveType' => $this->leaveRequest->leaveType->name,
                'startDate' => $this->leaveRequest->start_date->format('M j, Y'),
                'endDate' => $this->leaveRequest->end_date->format('M j, Y'),
                'duration' => $this->leaveRequest->duration,
                'approverName' => $this->approverName
            ]);
    }
}