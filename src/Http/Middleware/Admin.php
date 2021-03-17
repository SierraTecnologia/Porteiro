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

    /**
     * Create a new filter instance.
     *
     * @param  Guard           $auth
     * @param  ResponseFactory $response
     * @return void
     */
    public function __construct(
        Guard $auth,
        ResponseFactory $response
    ) {
        $this->auth = $auth;
        $this->response = $response;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('app.env') !== 'production') return $next($request); // @debug @todo
        // dd('adminrica', $this->auth->check());
        if ($this->auth->check()) {
            $admin = (int) $this->auth->user()->admin;

            if ($admin<1) {
                Log::info('Usuario sem permissão para admin, redirecionando! ');
                return $this->response->redirectTo('/');
            }

            return $next($request);
        }
        Log::info('Sem permissão para admin, redirecionando! ');
        // return response()->view('errors.401', [], 401);
        return $this->response->redirectTo('/');
    }
}
