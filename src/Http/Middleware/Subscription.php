<?php
/**
 * @todo tem que fazer aqui
 */

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

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @param  ResponseFactory  $response
     * @return void
     */
    public function __construct(Guard $auth,
                                ResponseFactory $response)
    {
        $this->auth = $auth;
        $this->response = $response;
    }
    /**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        // if ($this->auth->check())
        // {
        //     $admin = (int) $this->auth->user()->admin;

        //     if($admin<1){

        //         $request->session()->flash('status', "You dont have permission!");
        //         return $this->response->redirectTo('/');
        //     }

            return $next($request);
        // }
        // $request->session()->flash('status', "You need login!");
        // return $this->response->redirectTo('/');
	}

}
