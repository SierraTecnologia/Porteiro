<?php

namespace Porteiro\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ActivateUserEmail extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }
}
