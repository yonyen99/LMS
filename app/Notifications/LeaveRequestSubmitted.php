<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class LeaveRequestSubmitted extends Notification
{
    use Queueable;

    protected $leaveRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct($leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'telegram'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Leave Request Submitted')
            ->line("A new leave request has been submitted by {$this->leaveRequest->user->name}.")
            ->line("Leave Type: {$this->leaveRequest->leaveType->name}")
            ->line("Start Date: {$this->leaveRequest->start_date}")
            ->line("End Date: {$this->leaveRequest->end_date}")
            ->line("Duration: {$this->leaveRequest->duration} days")
            ->line("Reason: {$this->leaveRequest->reason}")
            ->action('View Leave Request', route('leave-requests.show', $this->leaveRequest))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        $message = "🔔 *New Leave Request Submitted*\n" .
                   "👤 *User*: {$this->leaveRequest->user->name}\n" .
                   "📅 *Leave Type*: {$this->leaveRequest->leaveType->name}\n" .
                   "🕒 *Start Date*: {$this->leaveRequest->start_date}\n" .
                   "🕔 *End Date*: {$this->leaveRequest->end_date}\n" .
                   "⏳ *Duration*: {$this->leaveRequest->duration} days\n" .
                   "📝 *Reason*: {$this->leaveRequest->reason}\n" .
                   "🔗 [View Request](" . route('leave-requests.show', $this->leaveRequest) . ")";

        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
            'disable_notification' => false,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'leave_request_id' => $this->leaveRequest->id,
            'user_name' => $this->leaveRequest->user->name,
            'leave_type' => $this->leaveRequest->leaveType->name,
            'start_date' => $this->leaveRequest->start_date,
            'end_date' => $this->leaveRequest->end_date,
            'duration' => $this->leaveRequest->duration,
            'reason' => $this->leaveRequest->reason,
        ];
    }
}