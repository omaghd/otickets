<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordReset extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $token)
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
        $resetUrl = getenv('FRONTEND_URL') . "/auth/reset-password?token=$this->token&email=$notifiable->email";
        $appName  = getenv('APP_NAME');
        return (new MailMessage)
            ->subject("Password Reset Request | $appName")
            ->greeting("Hello $notifiable->name,")
            ->line('Please click on the following link to reset your password.')
            ->action('Reset Password', $resetUrl)
            ->line('Thank you for choosing our services!')
            ->salutation('Kind Regards,');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'You have requested a password reset',
        ];
    }
}
