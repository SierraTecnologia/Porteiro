<?php

namespace Porteiro\Models;

use Illuminate\Database\Eloquent\Model;
use Porteiro\Facades\Porteiro;
use Population\Manipule\Builders\RoleBuilder;

class Role extends Model
{

    /**
     * Acesso Deus para usuários de Infra
     *
     * @var array
     */
    public static $GOOD = 1;

    /**
     * Acesso Deus para usuários de Infra
     *
     * @var array
     */
    public static $ADMIN = 2;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string[]
     *
     * @psalm-var array{0: 'name'}
     */
    protected $fillable = [
        'name'
    ];


    /**
     * @todo repetida pq da pau findByName
     */
    /**
     * @inheritdoc
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newEloquentBuilder($query): \Illuminate\Database\Eloquent\Builder
    {
        if (class_exists(\Population\Manipule\Builders\RoleBuilder::class)) {
            return new \Population\Manipule\Builders\RoleBuilder($query);
        }

        return parent::newEloquentBuilder($query);
    }

    /**
     * @inheritdoc
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::newQuery();
    }
}
