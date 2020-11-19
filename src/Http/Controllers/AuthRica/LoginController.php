<?php

namespace Porteiro\Http\Controllers\AuthRica;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Auth;
use Former;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
            'name' => 'required|max:255',
            'email' => 'required|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create(
            [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            ]
        );
    }


    public function login(Request $request)
    {
        $email      = $request->get('email');
        $password   = $request->get('password');
        $remember   = $request->get('remember');

        if (Auth::attempt(
            [
            'email'     => $email,
            'password'  => $password
            ],
            $remember == 1 ? true : false
        )
        ) {
            return redirect()->route('rica.dashboard');
            if (Auth::user()->hasRole('root')) {
                return redirect()->route('rica.dashboard');
            }

            if (Auth::user()->hasRole('administrator')) {
                return redirect()->route('rica.dashboard');
            }

            return redirect()->route('rica.dashboard');
        }
        

        return redirect()->back()
            ->with('message', trans('default.incorrect_email_or_password'))
            ->with('status', 'danger')
            ->withInput();
    }


    /**
     * Abaixo peguei do Decoy
     */
    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        // Pass validation rules
        Former::withRules(
            array(
            'email'    => 'required|email',
            'password' => 'required',
            )
        );
        if (view()->exists('porteiro::account.login')) {
            // Show the login homepage
            return view(
                'pedreiro::layouts.decoy.blank',
                [
                'content' => view('porteiro::account.login'),
                ]
            );
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        // Logout the session
        Auth::logout();

        // Redirect back to previous page so that switching users takes you back to
        // your previous page.
        $previous = url()->previous();
        if ($previous == url('/')) {
            return redirect(route('login'));
        }

        return redirect($previous);
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
        return route('login');
    }
}
