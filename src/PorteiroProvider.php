<?php

namespace Porteiro;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Muleta\Traits\Providers\ConsoleTools;
use Porteiro\Facades\Porteiro as PorteiroFacade;
use Porteiro\Http\Middleware\RiCa as RiCaMiddleware;
use Porteiro\Http\Middleware\Admin as AdminMiddleware;
use Porteiro\Http\Middleware\Master as MasterMiddleware;
use Porteiro\Http\Middleware\Painel as PainelMiddleware;
use Porteiro\Http\Middleware\Client as ClientMiddleware;
use Porteiro\Http\Middleware\User as UserMiddleware;
use Porteiro\Http\Middleware\Subscription as SubscriptionMiddleware;
use Porteiro\Services\PorteiroService;

class PorteiroProvider extends ServiceProvider
{
    use ConsoleTools;

    public $packageName = 'porteiro';
    const pathVendor = 'sierratecnologia/porteiro';

    public static $aliasProviders = [
        'Porteiro' => \Porteiro\Facades\Porteiro::class,
    ];

    public static $providers = [

        \Pedreiro\PedreiroServiceProvider::class,
        // HAML
        \Bkwld\LaravelHaml\ServiceProvider::class,

        
    ];
    /**
     * Rotas do Menu
     */
    public static $menuItens = [
        [
            'text' => 'Cadastros',
            'icon' => 'fas fa-fw fa-search',
            'icon_color' => "blue",
            'label_color' => "success",
            'section'   => 'admin',
            'level'       => 2, // 0 (Public), 1, 2 (Admin) , 3 (Root)
        ],
        [
            'text' => 'Acessos',
            'icon' => 'fas fa-fw fa-search',
            'icon_color' => "blue",
            'label_color' => "success",
            'section'   => 'admin',
            'level'       => 2, // 0 (Public), 1, 2 (Admin) , 3 (Root)
        ],
        'Cadastros' => [
            [
                'text'        => 'Usuários',
                'route'       => 'admin.porteiro.users.index',
                'icon'        => 'laptop',
                'icon_color'  => 'red',
                'label_color' => 'success',
                'section'     => 'admin',
                // 'level'       => 2,
                // 'feature' => 'commerce',
            ],
        ],
        'Acessos' => [
            [
                'text'        => 'Permissões',
                'route'       => 'admin.porteiro.permissions.index',
                'icon'        => 'laptop',
                'icon_color'  => 'red',
                'label_color' => 'success',
                'section'     => 'admin',
                // 'level'       => 2,
                // 'feature' => 'commerce',
            ],
        ],
    ];
    // /**
    //  * Indicates if loading of the provider is deferred.
    //  *
    //  * @var bool
    //  */
    // protected $defer = false;

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }


        /**
         * Porteiro; Routes
         */
        $this->loadRoutesForRiCa(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'routes');
    }
    /**
     * Bootstrap the application events.
     */
    public function boot(Router $router, Dispatcher $events)
    {
        $this->publishes(
            [
            __DIR__.'/../publishes/config/porteiro.php' => config_path('porteiro.php'),
            ],
            'config'
        );

        // $this->app['router']->aliasMiddleware('user', UserMiddleware::class);
        // $this->app['router']->aliasMiddleware('admin', AdminMiddleware::class);
        // $this->app['router']->pushMiddlewareToGroup('web', 'user');
        // $this->app['router']->pushMiddlewareToGroup('web', 'admin');
        $this->app['router']->middlewareGroup(
            'user',
            [
                'web',
                UserMiddleware::class
            ]
        );
        $this->app['router']->middlewareGroup(
            'client',
            [
                'web',
                ClientMiddleware::class
            ]
        );
        $this->app['router']->middlewareGroup(
            'painel',
            [
                'web',
                PainelMiddleware::class
            ]
        );
        $this->app['router']->middlewareGroup(
            'master',
            [
                'web',
                MasterMiddleware::class
            ]
        );
        $this->app['router']->middlewareGroup(
            'admin',
            [
                'web',
                AdminMiddleware::class
            ]
        );
        $this->app['router']->middlewareGroup(
            'rica',
            [
                'web',
                RiCaMiddleware::class
            ]
        );
        // Repete por causa de conflitos
        $this->app['router']->middlewareGroup(
            'root',
            [
                'web',
                RiCaMiddleware::class
            ]
        );

        $this->app['router']->middlewareGroup(
            'subscription',
            [
                'web',
                SubscriptionMiddleware::class
            ]
        );

        // View::composer(
        //     'kanban', 'App\Http\ViewComposers\KanbanComposer'
        // );
        // View::share('key', 'value');
        // Validator::extend('porteiro', function ($attribute, $value, $parameters, $validator) {
        // });
        
        $this->loadMigrationsFrom(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations/');
        $this->publishes(
            [
            __DIR__.'/../database/migrations/' => database_path('migrations')
            ],
            'migrations'
        );
        
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'porteiro');
        $this->publishes(
            [
            __DIR__.'/../resources/lang' => resource_path('lang'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'porteiro'),
            ]
        );

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'porteiro');
        $this->publishes(
            [
            __DIR__.'/../resources/views' => resource_path('views'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'porteiro'),
            ]
        );


        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    
                ]
            );
        }

        // Assets

        $this->publishes(
            [
            __DIR__.'/../publishes/assets' => public_path('vendor/porteiro'),
            ],
            'public'
        );

        // COloquei no register pq nao tava reconhecendo as rotas para o adminlte
        $this->app->booted(function () {
            $this->routes();
        });


        $events->listen(
            BuildingMenu::class,
            function (BuildingMenu $event) {
                (new \Pedreiro\Template\Mounters\SystemMount())->loadMenuForAdminlte($event);
            }
        );
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $loader = AliasLoader::getInstance();
        $this->mergeConfigFrom(
            __DIR__.'/../publishes/config/porteiro.php',
            'porteiro'
        );


        $loader->alias('Porteiro', PorteiroFacade::class);
        $this->app->singleton(
            'porteiro',
            function ($app) {
                return app()->make(PorteiroService::class);
            }
        );

        $this->app->singleton(
            AdminLte::class,
            function (Container $app) {
                return new AdminLte(
                    $app['config']['adminlte.filters'],
                    $app['events'],
                    $app
                );
            }
        );




        // Return the active user account
        $this->app->singleton(
            'facilitador.user', function ($app) {
                $guard = \Illuminate\Support\Facades\Config::get('application.auth.guard', 'facilitador');
                // dd('AppContainerGuardFacilitadorUser',$app['auth']->guard($guard)->user(), \Illuminate\Support\Facades\Config::get('application.auth.guard', 'facilitador'));
                return \App\Models\User::first(); //$app['auth']->guard($guard)->user(); // tinha isso aqui tirei 
            }
        );

        // Return a redirect response with extra stuff
        $this->app->singleton(
            'facilitador.acl_fail', function ($app) {
                return $app['redirect']
                    ->guest(route('login'))
                    ->withErrors([ 'error message' => __('pedreiro::login.error.login_first')]);
            }
        );

        // Build the Elements collection
        $this->app->singleton(
            'facilitador.elements', function ($app) {
                return with(new \Pedreiro\Collections\Elements)->setModel(\Support\Models\Element::class);
            }
        );
    }
    

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'porteiro',
            'facilitador.acl_fail',
            'facilitador.elements',
            'facilitador.user',
        ];
    }
}
