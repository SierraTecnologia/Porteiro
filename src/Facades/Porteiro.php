<?php

namespace Porteiro\Facades;

use Illuminate\Support\Facades\Facade;

class Porteiro extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'porteiro';
    }
}
