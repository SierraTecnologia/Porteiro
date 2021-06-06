<?php

namespace Porteiro\Http\Controllers\User;

use App\Http\Requests;
use App\Http\Requests\UserUpdateRequest;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Porteiro\Http\Controllers\User\Controller;
use Porteiro\Services\UserService;
use Templeiro;

class SettingsController extends Controller
{
    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }

    /**
     * View current user's settings
     *
     * @return \Illuminate\Http\Response
     */
    public function settings(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            return view(Templeiro::loadRelativeView('user.settings'))
            ->with('user', $user);
        }

        return back()->withErrors(['Could not find user']);
    }

    /**
     * Update the user
     *
     * @param  UpdateAccountRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request)
    {
        try {
            if ($this->service->update(auth()->id(), $request->all())) {
                return back()->with('message', 'Settings updated successfully');
            }

            return back()->withErrors(['Could not update user']);
        } catch (Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }
}
