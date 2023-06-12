<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
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
        $loginUrl = getenv('FRONTEND_URL') . '/login';
        $appName  = getenv('APP_NAME');
        return (new MailMessage)
            ->subject("Your password has been changed | $appName")
            ->greeting("Hello $notifiable->name,")
            ->line('Your password has been changed.')
            ->action('Login', $loginUrl)
            ->line('Thank you for choosing our services!')
            ->salutation('Kind Regards,');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your password has been changed',
        ];
    }
}
