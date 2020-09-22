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

    /**
     * Handle the user "created" event.
     *
     * @param  \App\Models\User $user
     * @return void
     */
    public function created(User $user)
    {
        $company = $user->getCompany();
        $token = '';
        $companyName = 'Produtora Desconhecida';
        if (!empty($company)) {
            $token = $company->token;
            $companyName = $company->nome;
        } else {
            Log::notice(
                'Usuário sem produtora. Cadastrando a padrão: '.
                env('DEFAULT_ORGANIZER_TOKEN').' User -> '.print_r($user, true)
            );
            $company = $user->setCompany(env('DEFAULT_ORGANIZER_TOKEN'));
            if (!empty($company)) {
                $token = $company->token;
                $companyName = $company->nome;
                Log::warning(
                    'Produtora não existente em usuário. User -> '.print_r($user, true)
                );
            }
        }

        // Envia SMS para Aprovar Usuário
        $user->notify(new PhoneActivate($company));

        // Registra no Payment via Job
        PaymentServiceRegisterUser::dispatch($user, $token);

        // Recupera Tickets antigos do Usuário
        UserRecoverOldTickets::dispatch($user, $token);

        // Envia Email de Bem Vindo
        // $user->notify(new WelcomeNewUser($user));
        $data_email = [];
        $data_email['name'] = $user->nome;
        $data_email['email'] = $user->email;
        $data_email['subject'] = $companyName .' - Bem vindo!';
        if (empty($company) || $company->id == 1) {
            PopEmail::send('email.welcome', $data_email, $company);
            return true;
        }
        PopEmail::send($company->slug .'.email.welcome', $data_email, $company);
        return true;
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Models\User $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\Models\User $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\Models\User $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\Models\User $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
