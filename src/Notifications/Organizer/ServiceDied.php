<?php

namespace Facilitador\Notifications\Organizer;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Zenvia\ZenviaChannel;
use NotificationChannels\Zenvia\ZenviaMessage;
use Illuminate\Support\Facades\Log;
use Siravel\Channels\SmsChannel;
use Siravel\Channels\Messages\SmsMessage;

class ServiceDied extends Notification implements ShouldQueue
{
    use Queueable;

    protected $_organizer = null;

    /**
     * Create a new notification instance.
     *
     * @param  $organizerToken
     * @return void
     */
    public function __construct($organizer)
    {
        $this->_organizer = $organizer;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [SmsChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toSms($notifiable)
    {
        $number = \App\Tools\Filter::phone($notifiable->telefone_celular);

        $nomeProdutora = '';
        if (!empty($this->_organizer) && isset($this->_organizer->nome)) {
            $nomeProdutora = $this->_organizer->nome;
        }

        $message = $notifiable->token_sms ." Token SMS ". $nomeProdutora .". Insira o token para validar sua conta.";
        Log::debug(
            '[Notification] Esqueci minha Senha > Mensagem: '.$message.
            ' Numero: '.$number
        );

        return SmsMessage::create()
            ->from('Passepague') // optional
            ->to($number) // your user phone
            ->content($message);
            // ->id('your-sms-id');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}