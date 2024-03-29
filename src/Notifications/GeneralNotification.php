<?php

namespace Porteiro\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    /**
     * General notification info
     *
     * @var string
     */
    public $info;

    /**
     * Create a notification instance.
     *
     * @param  string $token
     * @return void
     */
    public function __construct($info)
    {
        $this->info = $info;
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
        return (new MailMessage)
            ->line('Hey '.$notifiable->name)
            ->line('We have a notfiication for you.')
            ->line($this->info['title'])
            ->line($this->info['details'])
            ->action('Visit here for more info', url('/user/notifications'));
    }
}
