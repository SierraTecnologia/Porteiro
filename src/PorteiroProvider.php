<?php

namespace Porteiro;

use Illuminate\Support\ServiceProvider;
use Muleta\Traits\Providers\ConsoleTools;
use Porteiro\Facades\Porteiro as PorteiroFacade;

class PorteiroProvider extends ServiceProvider
{
    use ConsoleTools;
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
     * @return string[]
     *
     * @psalm-return array{0: string}
     */
    public function provides()
    {
        return ['porteiro'];
    }
}
