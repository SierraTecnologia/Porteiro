<?php

namespace Porteiro\Services;

/**
 *
 */
class PorteiroService
{
    protected $config;

    public function __construct($config = false)
    {
        if (!$this->config = $config) {
            $this->config = \Illuminate\Support\Facades\Config::get('porteiro');
        }
    }
}
