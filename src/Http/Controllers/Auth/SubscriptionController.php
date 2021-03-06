<?php

namespace Porteiro\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PleaseConfirmYourEmail;
use App\Models\User;
use Porteiro\Models\Role;
use Illuminate\Foundation\Auth\SubscriptsUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Porteiro\Traits\CaptchaTrait;
use Porteiro\Traits\ActivationTrait;

use App\Http\Requests\SubscriptionRequest;

use App\Models\Commerce\Plan;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Subscription Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

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
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        if (!$userMeta = $user->userMeta()->first()) {
            $plans = Plan::all();
            // return \Templeiro::view('user.subscription-register', compact('plans'));
        }

        return \Templeiro::view('user.subscription', compact('userMeta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function subscription(SubscriptionRequest $request)
    {
        $user = Auth::user();
        $user->userMeta()->create(
            [
            'is_active' => true
            ]
        );

        return redirect('/subscription');
    }

}
