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
     * São consumidores dos Clientes dos Usuários do Payment
     *
     * @var array
     */
    public static $CUSTOMER = 3;

    /**
     * Usuários do Organização
     *
     * @var array
     */
    public static $USER = 4;

    /**
     * São clientes dos Usuários do Payment.
     *
     * @var array
     */
    public static $CLIENT = 5;

    protected $guarded = [];

    protected $fillable = [
        'name'
    ];

    public function users()
    {
        $userModel = Porteiro::modelClass('User');

        return $this->belongsToMany($userModel, 'role_user')
            ->select(app($userModel)->getTable().'.*')
            ->union($this->hasMany($userModel))->getQuery();
    }

    public function permissions()
    {
        return $this->belongsToMany(Porteiro::modelClass('Permission'));
    }


    /**
     * @todo repetida pq da pau findByName
     */

    /**
     * Find Role by name
     *
     * @param string $name
     *
     * @return \Illuminate\Support\Collection|null|static|Role
     */
    public function findByName($name)
    {
        return $this->where('name', $name)->firstOrFail();
    }
    /**
     * @inheritdoc
     */
    public function newEloquentBuilder($query): RoleBuilder
    {
        return new RoleBuilder($query);
    }

    /**
     * @inheritdoc
     */
    public function newQuery(): RoleBuilder
    {
        return parent::newQuery();
    }
}
