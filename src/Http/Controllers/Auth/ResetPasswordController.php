<?php

namespace Porteiro\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use View;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function showResetForm(Request $request, $token = null){
        if (View::exists('auth.passwords.reset')) {
            return view('auth.passwords.reset')->with(
                ['token' => $token, 'email' => $request->email]
            );
        }
        return view('porteiro::auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
