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

    /**
     * @var string
     *
     * @psalm-var 'porteiro'
     */
    public $packageName = 'porteiro';
    const pathVendor = 'sierratecnologia/porteiro';

    /**
     * @var string[]
     *
     * @psalm-var array{Porteiro: Facades\Porteiro::class}
     */
    public static $aliasProviders = [
        'Porteiro' => \Porteiro\Facades\Porteiro::class,
    ];

    /**
     * @var string[]
     *
     * @psalm-var array{0: \Pedreiro\PedreiroServiceProvider::class}
     */
    public static $providers = [

        \Pedreiro\PedreiroServiceProvider::class,


    ];


    /**
     * Alias the services in the boot.
     */
    public function boot()
    {

        // Register configs, migrations, etc
        $this->registerDirectories();

        // COloquei no register pq nao tava reconhecendo as rotas para o adminlte
        $this->app->booted(
            function () {
                $this->routes();
            }
        );
    }

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
                \Illuminate\Support\Facades\Config::get('application.auth.guard', 'facilitador');
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
    }


    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     *
     * @psalm-return array{0: 'porteiro', 1: 'facilitador.acl_fail', 2: 'facilitador.user'}
     */
    public function provides()
    {
        return [
            'porteiro',
            'facilitador.acl_fail',
            'facilitador.user',
        ];
    }
    /**
     * Register configs, migrations, etc
     *
     * @return void
     */
    public function registerDirectories()
    {
        // Publish config files
        $this->publishes(
            [
            // Paths
            $this->getPublishesPath('config'.DIRECTORY_SEPARATOR.'porteiro.php') => config_path('porteiro.php'),
            ],
            ['config',  'sitec', 'sitec-config']
        );

        // // Publish porteiro css and js to public directory
        // $this->publishes([
        //     $this->getDistPath('porteiro') => public_path('assets/porteiro')
        // ], ['public',  'sitec', 'sitec-public']);

        $this->loadViews();
        $this->loadTranslations();
        // Register Migrations
        $this->loadMigrationsFrom(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations');
    }

    private function loadViews()
    {
        // View namespace
        $viewsPath = $this->getResourcesPath('views');
        $this->loadViewsFrom($viewsPath, 'porteiro');
        $this->publishes(
            [
            $viewsPath => base_path('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'porteiro'),
            ],
            ['views',  'sitec', 'sitec-views']
        );
    }

    private function loadTranslations()
    {
        // Publish lanaguage files
        $this->publishes(
            [
            $this->getResourcesPath('lang') => resource_path('lang'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'porteiro')
            ],
            ['lang',  'sitec', 'sitec-lang', 'translations']
        );

        // Load translations
        $this->loadTranslationsFrom($this->getResourcesPath('lang'), 'porteiro');
    }
}
