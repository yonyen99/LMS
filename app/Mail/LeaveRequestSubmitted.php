<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class LeaveRequestSubmitted extends Mailable
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
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Leave Request',
        );
    }



    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.leave_request_submitted',
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

    public function build()
    {
        $acceptUrl = URL::temporarySignedRoute(
            'leave-request.email.accept',
            now()->addDays(7),
            ['id' => $this->leaveRequest->id]
        );
        $rejectUrl = URL::temporarySignedRoute(
            'leave-request.email.reject',
            now()->addDays(7),
            ['id' => $this->leaveRequest->id]
        );

        $admins = \App\Models\User::role(['Super Admin', 'Manager'])->get();

        return $this->view('emails.leave_request_submitted')
            ->with([
                'leaveRequest' => $this->leaveRequest,
                'admins' => $admins,
                'acceptUrl' => $acceptUrl,
                'rejectUrl' => $rejectUrl,
            ]);
    }
}
