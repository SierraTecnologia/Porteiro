<?php

namespace Porteiro\Notifications\Organizer;

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
}