<?php

namespace Porteiro\Facades;

use Illuminate\Support\Facades\Facade;

class Porteiro extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @psalm-return 'porteiro'
     */
    protected static function getFacadeAccessor()
    {
        return 'porteiro';
    }
}
