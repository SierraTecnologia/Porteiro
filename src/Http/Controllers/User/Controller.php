<?php

namespace Porteiro\Http\Controllers\User;

use Porteiro\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
    }
}
