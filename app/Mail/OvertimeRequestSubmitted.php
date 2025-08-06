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

    /**
     * Create a new message instance.
     *
     * @param OvertimeRequest $overtime
     */
    public function __construct(OvertimeRequest $overtime)
    {
        $this->overtime = $overtime;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Overtime Request Submitted')
                    ->view('emails.overtime_request_submitted')
                    ->with([
                        'overtime' => $this->overtime,
                        'user' => $this->overtime->user,
                        'department' => $this->overtime->department,
                    ]);
    }
}