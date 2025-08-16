<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DepartmentLeaveNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveRequest;
    public $approverName;
    public $departmentName;

    /**
     * Create a new message instance.
     *
     * @param LeaveRequest $leaveRequest
     * @param string $approverName
     */
    public function __construct(LeaveRequest $leaveRequest, string $approverName)
    {
        $this->leaveRequest = $leaveRequest;
        $this->approverName = $approverName;
        $this->departmentName = $leaveRequest->user->department->name ?? 'Department';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Department Notification: Leave Request Approved - {$this->leaveRequest->user->name}")
                   ->markdown('emails.leave-accepted-department')
                   ->with([
                       'leaveRequest' => $this->leaveRequest,
                       'approverName' => $this->approverName,
                       'departmentName' => $this->departmentName,
                       'employeeName' => $this->leaveRequest->user->name,
                       'employeeEmail' => $this->leaveRequest->user->email
                   ]);
    }
}