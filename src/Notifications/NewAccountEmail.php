<?php

namespace Porteiro\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewAccountEmail extends Notification
{
    /**
     * The password
     *
     * @var string
     */
    public $password;

    /**
     * Create a notification instance.
     *
     * @param  string $token
     * @return void
     */
    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     *
     * @return string[]
     *
     * @psalm-return array{0: 'mail'}
     */
    public function via($notifiable): array
    {
        return [
            'mail'
        ];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return MailMessage
     */
    public function toMail($notifiable): self
    {
        return (new MailMessage())
            ->greeting('Hey '.$notifiable->name)
            ->line('You\'ve been given a new account on '.url('/'))
            ->line('EM: '.$notifiable->email)
            ->line('PW: '.$this->password)
            ->line('Click the link below to login')
            ->action('Login', url('login'));
    }
}
