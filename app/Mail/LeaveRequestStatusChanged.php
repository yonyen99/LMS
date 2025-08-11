<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveRequestStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveRequest;
    public $actionUser;

    /**
     * Create a new message instance.
     */
    public function __construct(LeaveRequest $leaveRequest, $actionUser)
    {
        $this->leaveRequest = $leaveRequest;
        $this->actionUser = $actionUser;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Leave Request {$this->leaveRequest->status} - {$this->leaveRequest->user->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.leave_request_status_changed',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}