<?php

namespace Porteiro\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Porteiro\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\UserService;
use DB;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use View;

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

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('guest');
        $this->service = $userService;
    }

    public function showRegistrationForm() {
        if (View::exists('auth.register')) {
            return view('auth.register');
        }
        return view('porteiro::auth.register');
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
            $data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return DB::transaction(
            function () use ($data) {
                $user = User::create(
                    [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => bcrypt($data['password'])
                    ]
                );
                try {
                    // $role = Role::firstOrNew(['name' => 'member']);
                    $role = Role::firstOrCreate(['name' => 'member']);
                    // $user->assignRole($role); // @todo Criada no UserServie Create
                } catch (\Throwable $th) {
                    \Log::error('Problema ao atribuir usuário');
                    \Log::error($th->getMessage());
                }

                return $this->service->create($user, $data['password'], 'member');
            }
        );
    }
}
