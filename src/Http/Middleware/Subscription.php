<?php 

namespace Porteiro\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Routing\Redirector;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Foundation\Applicaion;


class Subscription
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
