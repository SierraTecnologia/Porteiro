<?php

namespace Porteiro\Services;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Translation;
use Illuminate\Support\Str;

/**
 *
 */
class PorteiroService
{
    protected $config;

    protected $models = [
        'Category'          => Category::class,
        'DataRow'           => DataRow::class,
        'DataRelationship'  => DataRelationship::class,
        'DataType'          => DataType::class,
        'Menu'              => Menu::class,
        'MenuItem'          => MenuItem::class,
        'Page'              => Page::class,
        'Permission'        => Permission::class,
        'Post'              => Post::class,
        'Role'              => Role::class,
        'Setting'           => Setting::class,
        'User'              => User::class,
        'Translation'       => Translation::class,
    ];

    public function __construct($config = false)
    {
        if (!$this->config = $config) {
            $this->config = \Illuminate\Support\Facades\Config::get('porteiro');
        }
    }

    public function model($name)
    {
        return app($this->models[Str::studly($name)]);
    }

    public function modelClass($name)
    {
        return $this->models[$name];
    }
}
