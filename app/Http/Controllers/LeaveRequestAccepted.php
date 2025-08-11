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

    /**
     * Create a new message instance.
     *
     * @param LeaveRequest $leaveRequest
     * @return void
     */
    public function __construct(LeaveRequest $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Leave Request Has Been Accepted')
                    ->view('emails.leave_request_accepted')
                    ->with([
                        'leaveRequest' => $this->leaveRequest,
                        'user' => $this->leaveRequest->user,
                    ]);
    }
}