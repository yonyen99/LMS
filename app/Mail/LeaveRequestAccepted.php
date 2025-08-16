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
        return $this->subject("Your Leave Request Has Been Approved")
            ->markdown('emails.leave_request_accepted')
            ->with([
                'leaveRequest' => $this->leaveRequest,
                'approverName' => $this->approverName
            ]);
    }
}