<?php

namespace Porteiro\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Porteiro\Activation\ActivationRepository;

trait ActivationTrait
{

    /**
     * @return null|true
     */
    public function initiateEmailActivation(User $user)
    {

        if (!config('settings.activation')  || !$this->validateEmail($user)) {

            return true;

        }

        $activationRepostory = new ActivationRepository();

        $activationRepostory->createTokenAndSendEmail($user);

    }

    protected function validateEmail(User $user): bool
    {

        // Check does email posses valid format, cause if it's social account without
        // email, it'll have value of missingxxxxxxxxxx
        $validator = Validator::make(['email' => $user->email], ['email' => 'required|email']);

        if ($validator->fails()) {
            return false; // Stopping job execution, if it return false it will break entire application
        }

        return true;

    }

}