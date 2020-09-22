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

    public static $aliasProviders = [
        'Porteiro' => \Porteiro\Facades\Porteiro::class,
    ];

    public static $providers = [

        \Pedreiro\PedreiroProviderService::class,

        
    ];
    /**
     * Rotas do Menu
     */
    public static $menuItens = [
        // [
        //     'text'        => 'Vendas',
        //     'url'         => 'admin/commerce-analytics',
        //     'icon'        => 'laptop',
        //     'icon_color'  => 'red',
        //     'label_color' => 'success',
        //     'section'     => 'painel',
        //     'level'       => 2,
        //     'feature' => 'commerce',
        // ],
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
    public function boot()
    {
        $this->publishes(
            [
            __DIR__.'/../publishes/config/porteiro.php' => config_path('porteiro.php'),
            ],
            'config'
        );

        // View::composer(
        //     'kanban', 'App\Http\ViewComposers\KanbanComposer'
        // );
        // View::share('key', 'value');
        // Validator::extend('porteiro', function ($attribute, $value, $parameters, $validator) {
        // });
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        
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
