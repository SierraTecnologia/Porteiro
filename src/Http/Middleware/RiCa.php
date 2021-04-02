<?php 
namespace Porteiro\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Log;

class RiCa
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
        // if (config('app.env') !== 'production') return $next($request); // @debug @todo
        if ($this->auth->check()) {
            $admin = (int) $this->auth->user()->admin;

            if (!$this->auth->user()->isRoot()) {
                Log::info('Usuario sem permissão para rica(root), redirecionando! ');
                return $this->response->redirectTo($this->auth->user()->homeUrl());
            }

            return $next($request);
        }
        Log::info('Sem permissão para rica, redirecionando! ');
        return $this->response->redirectTo('/');
    }
}
