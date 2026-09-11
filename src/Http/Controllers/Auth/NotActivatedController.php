<?php

namespace Porteiro\Http\Controllers\Auth;

class NotActivatedController
{
    public function __invoke()
    {
        return response()->view('errors.not-activated', [], 403);
    }
}
