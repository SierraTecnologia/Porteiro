<?php

namespace Porteiro\Http\Controllers\User;

use Porteiro\Traits\ActivationTrait;
use App\Models\Activation;
use Porteiro\Http\Controllers\User\Controller;

class ActivateController extends Controller
{

    use ActivationTrait;

    public function activate($token)
    {
        if (auth()->user()->activated) {

            return redirect()->route('public.home')
                ->with('status', 'success')
                ->with('message', trans('default.email_already_activated'));
        }

        $activation = Activation::where('token', $token)
            ->where('user_id', auth()->user()->id)
            ->first();

        if (empty($activation)) {

            return redirect()->route('public.home')
                ->with('status', 'wrong')
                ->with('message', trans('default.no_found_token'));

        }

        auth()->user()->activated = true;
        auth()->user()->save();

        $activation->delete();

        session()->forget('above-navbar-message');

        return redirect()->route('public.home')
            ->with('status', 'success')
            ->with('message', trans('default.successfully_activated_email'));

    }

    public function resend()
    {
        if (auth()->user()->activated == false) {
            $this->initiateEmailActivation(auth()->user());

            return redirect()->route('public.home')
                ->with('status', 'success')
                ->with('message', trans('default.activation_email_sent'));
        }

        return redirect()->route('public.home')
            ->with('status', 'success')
            ->with('message', trans('default.already_activated'));
    }
}