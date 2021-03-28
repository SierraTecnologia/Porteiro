<?php

namespace Porteiro\Http\Controllers\Auth;

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
use View;

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

    public function showLoginForm()
    {
        if (View::exists('auth.login')) {
            return view('auth.login');
        }
        return view('porteiro::auth.login');
    }

    /**
     * Check user's role and redirect user based on their role
     *
     * @return
     */
    public function authenticated()
    {
        if (auth()->user()->hasRole('admin')) {
            return redirect('/admin/dashboard');
        }

        return redirect('dashboard');
    }

    public function login(Request $request)
    {
        $email      = $request->get('email');
        $password   = $request->get('password');
        $remember   = $request->get('remember');

        // dd('oiuiuiui');
        if (Auth::attempt(
            [
            'email'     => $email,
            'password'  => $password
            ],
            $remember == 1 ? true : false
        )
        ) {
            return redirect()->route('admin.porteiro.dashboard');
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

}
