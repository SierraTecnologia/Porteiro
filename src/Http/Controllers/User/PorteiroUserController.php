<?php

namespace Porteiro\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Porteiro\Facades\Porteiro;
use Support\Routing\UrlGenerator;
use Porteiro\Http\Controllers\User\Controller;

class PorteiroUserController extends Controller
{
    public function profile(Request $request)
    {
        $route = '';
        $dataType = Porteiro::model('DataType')->where('model_name', Auth::guard(app('PorteiroGuard'))->getProvider()->getModel())->first();
        if (!$dataType && app('PorteiroGuard') == 'web') {
            $route = route('facilitador.users.edit', Auth::user()->getKey());
        } elseif ($dataType) {
            $route = UrlGenerator::managerRoute($dataType->slug, 'edit', Auth::user()->getKey());
            // $route = \Support\Routing\UrlGenerator::managerRoute($dataType->slug, 'edit', Auth::user()->getKey());
        }

        return Porteiro::view('facilitador::profile', compact('route'));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        if (Auth::user()->getKey() == $id) {
            $request->merge(
                [
                'role_id'                              => Auth::user()->role_id,
                'user_belongstomany_role_relationship' => Auth::user()->roles->pluck('id')->toArray(),
                ]
            );
        }

        return parent::update($request, $id);
    }
}
