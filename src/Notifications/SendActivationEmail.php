<?php

namespace Porteiro\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendActivationEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $token;

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     *
     * @psalm-return array<empty, empty>
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
