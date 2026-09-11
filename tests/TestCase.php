<?php

namespace Porteiro\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Porteiro\PorteiroProvider;
use Pedreiro\PedreiroServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            PorteiroProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('auth.providers.users.model', \Porteiro\Models\User::class);
        $app['config']->set('adminlte.filters', []);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
