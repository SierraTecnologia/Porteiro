<?php

namespace Porteiro\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
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
    // /**
    //  * Build the mail representation of the notification.
    //  *
    //  * @param  mixed $notifiable
    //  * @return \Illuminate\Notifications\Messages\MailMessage
    //  */
    // public function toMail($notifiable)
    // {
    //     // Make the URL
    //     $dir = \Illuminate\Support\Facades\Config::get('application.routes.main');
    //     $url = url($dir.'/password/reset', $this->token);

    //     // Send the message
    //     return (new MailMessage)
    //         ->subject('Recover access to '.Porteiro::site())
    //         ->line('You are receiving this email because we received a password reset request for your account.')
    //         ->action('Reset Password', $url)
    //         ->line('If you did not request a password reset, no further action is required.');
    // }
}