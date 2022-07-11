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


    /**
     * @var string[][]
     *
     * @psalm-var array{Permission: array{0: 'App\Models\Permission', 1: 'Porteiro\Models\Permission'}, Role: array{0: 'App\Models\Role', 1: 'Porteiro\Models\Role'}, User: array{0: 'App\Models\User', 1: 'Porteiro\Models\User'}}
     */
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
}
