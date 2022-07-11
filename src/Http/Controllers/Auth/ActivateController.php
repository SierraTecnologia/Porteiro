<?php

namespace Porteiro\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Porteiro\Services\ActivateService;
use App\Services\UserService;

class ActivateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ActivateService $activateService)
    {
        $this->service = $activateService;
    }

    /**
     * Inform the user they must activate thier account
     *
     * @return \Illuminate\Http\Response
     */
    public function showActivate()
    {
        return \Templeiro::view('auth.activate.email');
    }

    /**
     * Send a new token for activation
     *
     * @return User
     */
    public function sendToken()
    {
        $this->service->sendActivationToken();
        return \Templeiro::view('auth.activate.token');
    }

    /**
     * Activate a user account
     *
     * @return User
     */
    public function activate($token)
    {
        if ($this->service->activateUser($token)) {
            return redirect('dashboard')->with('message', 'Your account was activated');
        }

        return \Templeiro::view('auth.activate.email')->withErrors(['Could not validate your token']);
    }
}
