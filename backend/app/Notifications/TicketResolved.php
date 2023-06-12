<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketResolved extends Notification implements ShouldQueue
{
    use Queueable;

    private string $markedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Ticket $ticket)
    {
        $this->markedBy = request()->user('sanctum')->isClient() ? 'client' : 'agent';
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
        $subUrl    = $notifiable->isAgent() ? '/dashboard/tickets/' : '/tickets/';
        $ticketUrl = getenv('FRONTEND_URL') . $subUrl . $reference;
        $appName   = getenv('APP_NAME');
        return (new MailMessage)
            ->subject("[REF #$reference] Ticket Resolved | $appName")
            ->greeting("Hello $notifiable->name,")
            ->lines([
                "The ticket (#$reference) has been marked as resolved by the $this->markedBy.",
                'Please click on the following link to view the ticket',
            ])
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
            'message'   => "The ticket (#$reference) has been marked as resolved by the $this->markedBy.",
        ];
    }
}
