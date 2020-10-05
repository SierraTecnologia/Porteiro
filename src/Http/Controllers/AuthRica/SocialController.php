<?php

namespace Porteiro\Http\Controllers\AuthRica;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use App\Http\ControllersController;
use Porteiro\Traits\ActivationTrait;
use App\Models\Social;
use App\Models\User;
use App\Models\Role;

class SocialController extends Controller
{

    use ActivationTrait;

    public function getSocialRedirect( $provider )
    {

        $providerKey = Config::get('services.' . $provider);

        if (empty($providerKey)) {

            return view('pages.status')
                ->with('error', trans('default.no_such_provider'));

        }

        return Socialite::driver($provider)->redirect();

    }

    public function getSocialHandle( $provider )
    {

        if (Input::get('denied') != '') {

            return redirect()->to('login')
                ->with('status', 'danger')
                ->with('message', trans('default.you_did_not_share_your_profile_with_social_app'));

        }

        $user = Socialite::driver($provider)->user();

        $socialUser = null;

        //Check is this email present
        $userCheck = User::where('email', '=', $user->email)->first();

        $email = $user->email;

        if (!$user->email) {
            $email = 'missing' . \Illuminate\Support\Str::random(10);
        }

        if (!empty($userCheck)) {

            $socialUser = $userCheck;

        }
        else {

            $sameSocialId = Social::where('social_id', '=', $user->id)
                ->where('provider', '=', $provider)
                ->first();

            if (empty($sameSocialId)) {

                //There is no combination of this social id and provider, so create new one
                $newSocialUser = new User;
                $newSocialUser->email              = $email;
                $newSocialUser->name              = $user->name;
                $newSocialUser->password = bcrypt(\Illuminate\Support\Str::random(16));
                $newSocialUser->token = \Illuminate\Support\Str::random(64);
                $newSocialUser->activated = true; //!\Illuminate\Support\Facades\Config::get('settings.activation');
                $newSocialUser->save();

                $socialData = new Social;
                $socialData->social_id = $user->id;
                $socialData->provider= $provider;
                $newSocialUser->social()->save($socialData);

                // Add role
                $role = Role::whereName('user')->first();
                $newSocialUser->assignRole($role);

                $this->initiateEmailActivation($newSocialUser);

                $socialUser = $newSocialUser;

            }
            else {

                //Load this existing social user
                $socialUser = $sameSocialId->user;

            }

        }

        auth()->login($socialUser, true);

        if (auth()->user()->hasRole('user')) {

            return redirect()->route('user.home');

        }

        if (auth()->user()->hasRole('administrator')) {

            return redirect()->route('admin.home');

        }

        return abort(500, trans('default.social_register_no_role'));

    }
}