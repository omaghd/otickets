<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreated extends Notification implements ShouldQueue
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
        $appName   = getenv('APP_NAME');
        return (new MailMessage)
            ->subject("[REF #$reference] New Ticket | $appName")
            ->greeting("Hello $notifiable->name,")
            ->lines([
                'Thank you for contacting our support team!',
                "This email is to confirm that we have received your ticket
                (#$reference) and it is currently being reviewed by our team.",
                'To view the status of your ticket, please click on the following link'
            ])
            ->action('View Ticket', $ticketUrl)
            ->lines([
                'You can track the progress of your request from there
                and also add any additional details by replying to the ticket.',
                'Please note that the email sender is no-reply.
                If you have any further questions or concerns,
                please reply to the ticket and our support team will assist you promptly.',
                'Thank you for choosing our services!'
            ])
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
            'message'   => "A new ticket (#$reference) has been created",
        ];
    }
}
