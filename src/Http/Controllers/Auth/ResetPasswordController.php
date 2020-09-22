<?php

namespace Porteiro\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

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

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string|null              $token
     * @return \Illuminate\Http\Response
     */
    public function showResetForm(Request $request, $token = null)
    {
        // Pass validation rules
        Former::withRules(
            [
            'email'                 => 'required|email',
            'password'              => 'required',
            'password_confirmation' => 'required|same:password',
            ]
        );

        // Set the breadcrumbs
        app('rica.breadcrumbs')->set(
            [
            route('facilitador.account@login') => 'Login',
            route('facilitador.account@forgot') => 'Forgot Password',
            url()->current() => 'Reset Password',
            ]
        );

        // Show the page
        $this->title = 'Reset Password';
        $this->description = 'Almost done.';

        return $this->populateView(
            'facilitador::account.reset', [
            'token' => $token,
            ]
        );
    }

    /**
     * Get the post register / login redirect path. This is set to the login route
     * so that the guest middleware can pick it up and redirect to the proper
     * start page.
     *
     * @return string
     */
    public function redirectPath()
    {
        return route('facilitador.account@login');
    }

    /**
     * Subclass the resetPassword method so that it doesn't `bcrypt()` the
     * password.  We're trusting to the model's onSaving callback for this.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param  string                                      $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill(
            [
            'password' => $password,
            'remember_token' => Str::random(60),
            ]
        )->save();

        Auth::login($user);
    }
}
