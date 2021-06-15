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

    /**
     * Create a new filter instance.
     *
     * @param  Guard           $auth
     * @param  ResponseFactory $response
     * @return void
     */
    public function __construct(Guard $auth,
        ResponseFactory $response
    ) {
        $this->auth = $auth;
        $this->response = $response;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // if (config('app.env') !== 'production') return $next($request); // @debug @todo
        
        if ($this->auth->check()) {
            // dd($this->auth->user()->userMeta()->first());
            if (!$userMeta = $this->auth->user()->userMeta()->first()) {
                Log::debug('Sem permissão para subscription, redirecionando! ');
                return $this->response->redirectTo('/subscription');
            }
            
            return $next($request);
        }
        return $this->response->redirectTo(route('login'));
    }

}
