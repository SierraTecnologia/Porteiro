<?php

namespace Porteiro\Http\Controllers\Auth;

use App\Http\ControllersController;
use Porteiro\Models\Role;
use App\Models\Social;
use App\Models\User;
use Porteiro\Traits\ActivationTrait;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends SitecController
{
    use ActivationTrait;

    public function getSocialRedirect($provider)
    {
        $providerKey = Config::get('services.' . $provider);

        if (empty($providerKey)) {
            return \Templeiro::view('pages.status')
                ->with('error', trans('default.no_such_provider'));
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function getSocialHandle($provider): ?self
    {
        if (Input::get('denied') != '') {
            return redirect()->to('login')
                ->with('status', 'danger')
                ->with('message', trans('default.you_did_not_share_your_profile_with_social_app'));
        }

        $user = Socialite::driver($provider)->user();


        //Check is this email present
        $userCheck = User::where('email', '=', $user->email)->first();

        $email = $user->email;

        if (!$user->email) {
            $email = 'missing' . str_random(10);
        }

        if (!empty($userCheck)) {
            $socialUser = $userCheck;
        } else {
            $sameSocialId = Social::where('social_id', '=', $user->id)
                ->where('provider', '=', $provider)
                ->first();

            if (empty($sameSocialId)) {

                //There is no combination of this social id and provider, so create new one
                $newSocialUser = new User;
                $newSocialUser->email              = $email;
                $newSocialUser->name              = $user->name;
                $newSocialUser->password = bcrypt(str_random(16));
                $newSocialUser->token = str_random(64);
                $newSocialUser->activated = true; //!config('settings.activation');
                $newSocialUser->save();

                $socialData = new Social;
                $socialData->social_id = $user->id;
                $socialData->provider= $provider;
                $newSocialUser->social()->save($socialData);

                // Add role
                $role = Role::where('name', 'user')->first();
                $newSocialUser->assignRole($role);

                $this->initiateEmailActivation($newSocialUser);

                $socialUser = $newSocialUser;
            } else {

                //Load this existing social user
                $socialUser = $sameSocialId->user;
            }
        }

        auth()->login($socialUser, true);

        if (auth()->user()->hasRole('user')) {
            return redirect()->route('user.home');
        }

        if (auth()->user()->hasRole('administrator')) {
            return redirect()->route('admin.porteiro.dashboard');
        }

        return abort(500, trans('default.social_register_no_role'));
    }
}
