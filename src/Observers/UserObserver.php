<?php

namespace Porteiro\Observers;

use App\Jobs\PaymentServiceRegisterUser;
use App\Jobs\UserRecoverOldTickets;
use App\Models\User;
use App\Tools\PopEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Siravel\Notifications\PhoneActivate;

class UserObserver implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
}
