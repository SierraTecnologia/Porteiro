<?php

namespace Porteiro\Services;

use Siravel\Models\Negocios\Menu;
use Siravel\Models\Negocios\MenuItem;
use App\Models\Permission;
use Porteiro\Models\Role;
use App\Models\Setting;
use App\Models\Translation;
use Auth;
use Illuminate\Support\Str;

/**
 *
 */
class PorteiroService
{
    protected $config;

    protected $models = [
        'Permission'        => [
            'App\Models\Permission',
            'Porteiro\Models\Permission',
        ],
        'Role'              => [
            'App\Models\Role',
            'Porteiro\Models\Role',
        ],
        'User'              => [
            'App\Models\User',
            'Porteiro\Models\User',
        ],
    ];

    public function __construct($config = false)
    {
        if (!$this->config = $config) {
            $this->config = \Illuminate\Support\Facades\Config::get('porteiro');
        }
    }

    public function model($name)
    {
        return app($this->modelClass($name));
    }

    /**
     * @return class-string
     */
    public function modelClass($name): string
    {
        $name = Str::studly($name);
        $classes = $this->models[$name];
        foreach ($classes as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }
        throw new \Exception('Porteiro: Classe não encontrada: '.$name);
    }

    public function canOrFail($codePermission)
    {
        if (!$user = Auth::user()) {
            throw new \Exception('Porteiro: Sem permissao: '.$codePermission);
        }
        return $user->can($codePermission);
    }
}
