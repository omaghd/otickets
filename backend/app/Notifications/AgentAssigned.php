<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgentAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Ticket $ticket)
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $reference = $this->ticket->reference;
        $ticketUrl = getenv('FRONTEND_URL') . '/dashboard/tickets/' . $reference;
        $appName   = getenv('APP_NAME');

        return (new MailMessage)
            ->subject("You've been assigned to a new ticket | $appName")
            ->greeting("Hello $notifiable->name,")
            ->line("You've been assigned to a new ticket (#$reference).")
            ->line('Please click on the following link to view the ticket')
            ->action('View Ticket', $ticketUrl)
            ->salutation('Kind Regards,');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $reference = $this->ticket->reference;
        return [
            'reference' => $reference,
            'message'   => "You've been assigned to a new ticket (#$reference).",
        ];
    }
}
