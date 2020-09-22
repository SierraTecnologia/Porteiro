<?php

namespace Porteiro\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PleaseConfirmYourEmail;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Porteiro\Traits\CaptchaTrait;
use Porteiro\Traits\ActivationTrait;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers, ActivationTrait, CaptchaTrait;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // return Validator::make($data, [
        //     'name' => 'required|max:255',
        //     'email' => 'required|email|max:255|unique:users',
        //     'password' => 'required|min:6|confirmed',
        // ]);


        $data['captcha'] = $this->captchaCheck();

        $validator = Validator::make(
            $data,
            [
                'first_name'            => 'required',
                'last_name'             => 'required',
                'email'                 => 'required|unique:users',
                'password'              => 'required|min:6|max:20',
                'password_confirmation' => 'required|same:password',
                'g-recaptcha-response'  => 'required',
                'captcha'               => 'required|min:1'
            ],
            [
                'first_name.required'   => trans('default.first_name_required'),
                'last_name.required'    => trans('default.last_name_required'),
                'email.required'        => trans('default.email_address_required'),
                'email.email'           => trans('default.email_address_invalid'),
                'password.required'     => trans('default.password_required'),
                'password.min'          => trans('default.password_needs'),
                'password.max'          => trans('default.password_maximum'),
                'g-recaptcha-response.required' => trans('default.captcha_required'),
                'captcha.min'           => trans('default.captcha_invalid')
            ]
        );

        return $validator;

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        $user =  User::create(
            [
            'name' => $data['name'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'token' => \Illuminate\Support\Str::random(64),
            'confirmation_token' => str_limit(md5($data['email'] . \Illuminate\Support\Str::random()), 25, ''),
            'activated' => !\Illuminate\Support\Facades\Config::get('settings.activation')
            ]
        );

        if (isset($data['avatar'])) {
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
        }

        $role = Role::whereName('user')->first();
        $user->assignRole($role);

        $this->initiateEmailActivation($user);

        return $user;
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User         $user
     * @return void
     */
    protected function registered(Request $request, $user)
    {
        Mail::to($user)->send(new PleaseConfirmYourEmail($user));
    }
}
