<?php

namespace Porteiro;

use Illuminate\Support\ServiceProvider;
use Muleta\Traits\Providers\ConsoleTools;
use Porteiro\Facades\Porteiro as PorteiroFacade;

class PorteiroProvider extends ServiceProvider
{
    use ConsoleTools;
    public $packageName = 'porteiro';
    const pathVendor = 'sierratecnologia/porteiro';

    /**
     * @var Facades\Porteiro::class[]
     *
     * @psalm-var array{Porteiro: Facades\Porteiro::class}
     */
    public static array $aliasProviders = [
        'Porteiro' => \Porteiro\Facades\Porteiro::class,
    ];

    /**
     * @var \Pedreiro\PedreiroProviderService::class[]
     *
     * @psalm-var array{0: \Pedreiro\PedreiroProviderService::class}
     */
    public static array $providers = [

        \Pedreiro\PedreiroProviderService::class,

        
    ];


    /**
     * Rotas do Menu
     */
    public static $menuItens = [
        'Tecnologia|10' => [
        ],
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
     * Register bindings in the container.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../publishes/config/porteiro.php',
            'porteiro'
        );
        // $this->app->singleton(
        //     AdminLte::class,
        //     function (Container $app) {
        //         return new AdminLte(
        //             $app['config']['adminlte.filters'],
        //             $app['events'],
        //             $app
        //         );
        //     }
        // );


        // Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->app->singleton(
            'porteiro',
            function () {
                return new Porteiro();
            }
        );
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     *
     * @psalm-return array{0: string}
     */
    public function provides()
    {
        return ['porteiro'];
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
            $this->getPublishesPath('config/sitec') => config_path('sitec'),
            ],
            ['config',  'sitec', 'sitec-config']
        );

        // // Publish fabrica css and js to public directory
        // $this->publishes([
        //     $this->getDistPath('fabrica') => public_path('assets/fabrica')
        // ], ['public',  'sitec', 'sitec-public']);

        $this->loadViews();
        $this->loadTranslations();
    }

    private function loadViews()
    {
        // View namespace
        $viewsPath = $this->getResourcesPath('views');
        $this->loadViewsFrom($viewsPath, 'fabrica');
        $this->publishes(
            [
            $viewsPath => base_path('resources/views/vendor/fabrica'),
            ],
            ['views',  'sitec', 'sitec-views']
        );
    }
    
    private function loadTranslations()
    {
        // Publish lanaguage files
        $this->publishes(
            [
            $this->getResourcesPath('lang') => resource_path('lang/vendor/fabrica')
            ],
            ['lang',  'sitec', 'sitec-lang', 'translations']
        );

        // Load translations
        $this->loadTranslationsFrom($this->getResourcesPath('lang'), 'fabrica');
    }
}
