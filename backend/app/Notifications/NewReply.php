<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReply extends Notification implements ShouldQueue
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
        $ticketUrl = getenv('FRONTEND_URL') . '/tickets/' . $reference;
        $line      = $notifiable->isClient()
            ? 'A new reply has been added to your ticket'
            : 'A new reply has been added to a ticket';
        $appName   = getenv('APP_NAME');
        return (new MailMessage)
            ->subject("[REF #$reference] New Reply | $appName")
            ->greeting("Hello $notifiable->name,")
            ->lineIf(
                $notifiable->isClient(),
                "A new reply has been added to your ticket (#$reference)."
            )
            ->lineIf(
                $notifiable->isAgent(),
                "A new reply has been added to a ticket you are assigned to (#$reference)."
            )
            ->action('View Ticket', $ticketUrl)
            ->line('Thank you for choosing our services!')
            ->salutation('Kind Regards,');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $reference = $this->ticket->reference;
        $message   = $notifiable->isClient()
            ? "A new reply has been added to your ticket (#$reference)"
            : "A new reply has been added to a ticket you are assigned to (#$reference)";
        return [
            'reference' => $reference,
            'message'   => $message,
        ];
    }
}
