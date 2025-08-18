<?php

namespace App\Mail;

use App\Models\OvertimeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OvertimeRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $overtime;
    public $user;
    public $department;

    /**
     * Create a new message instance.
     */
    public function __construct(OvertimeRequest $overtime)
    {
        $this->overtime = $overtime;
        $this->user = $overtime->user;
        $this->department = $overtime->department;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Overtime Request Submitted')
            ->view('emails.overtime_request_submitted')
            ->with([
                'overtime'   => $this->overtime,
                'user'       => $this->user,
                'department' => $this->department,
            ]);
    }
}
