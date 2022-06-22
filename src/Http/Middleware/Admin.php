<?php namespace Porteiro\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Log;

class Admin
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The response factory implementation.
     *
     * @var ResponseFactory
     */
    protected $response;

}
