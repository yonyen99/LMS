<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(LeaveRequest $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Leave Request Approved')
            ->markdown('emails.leave_request_accepted');
    }
}
