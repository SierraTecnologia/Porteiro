<?php

namespace Porteiro\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class User extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return null|string
     */
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
