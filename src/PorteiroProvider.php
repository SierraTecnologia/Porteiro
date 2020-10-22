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
use Porteiro\Http\Middleware\Admin as AdminMiddleware;
use Porteiro\Http\Middleware\User as UserMiddleware;
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

        \Pedreiro\PedreiroProviderService::class,
        // HAML
        \Bkwld\LaravelHaml\ServiceProvider::class,

        
    ];
    /**
     * Rotas do Menu
     */
    public static $menuItens = [
        [
            'text'        => 'Usuários',
            'route'       => 'admin.porteiro.users.index',
            'icon'        => 'laptop',
            'icon_color'  => 'red',
            'label_color' => 'success',
            // 'section'     => 'painel',
            // 'level'       => 2,
            // 'feature' => 'commerce',
        ],
        [
            'text'        => 'Permissões',
            'route'       => 'admin.porteiro.permissions.index',
            'icon'        => 'laptop',
            'icon_color'  => 'red',
            'label_color' => 'success',
            // 'section'     => 'painel',
            // 'level'       => 2,
            // 'feature' => 'commerce',
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
        $this->loadRoutesForRiCa(__DIR__.'/../routes');
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
            'admin',
            [
                'web',
                AdminMiddleware::class
            ]
        );

        // View::composer(
        //     'kanban', 'App\Http\ViewComposers\KanbanComposer'
        // );
        // View::share('key', 'value');
        // Validator::extend('porteiro', function ($attribute, $value, $parameters, $validator) {
        // });
        
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/');
        $this->publishes(
            [
            __DIR__.'/../database/migrations/' => database_path('migrations')
            ],
            'migrations'
        );
        
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'porteiro');
        $this->publishes(
            [
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/porteiro'),
            ]
        );

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'porteiro');
        $this->publishes(
            [
            __DIR__.'/../resources/views' => resource_path('views/vendor/porteiro'),
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
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['porteiro'];
    }
}
