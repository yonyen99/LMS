<?php

namespace App\Mail;

use App\Models\OvertimeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OvertimeRequestStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $overtime;

    public function __construct(OvertimeRequest $overtime)
    {
        $this->overtime = $overtime;
    }

    public function build()
    {
        return $this->subject('Overtime Request Status Updated')
                    ->view('emails.overtime_request_status_updated')
                    ->with([
                        'overtime' => $this->overtime,
                        'user' => $this->overtime->user,
                        'department' => $this->overtime->department,
                    ]);
    }
}