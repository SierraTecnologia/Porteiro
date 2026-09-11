<?php

namespace Porteiro\Http\Controllers\Auth;

use Illuminate\Http\Request;

class PusherAuthController
{
    public function __invoke(Request $request)
    {
        return $request->user();
    }
}
