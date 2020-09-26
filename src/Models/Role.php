<?php

namespace Porteiro\Models;

use Illuminate\Database\Eloquent\Model;
use Porteiro\Facades\Porteiro;

class Role extends Model
{

    /**
     * @var array
     */
    protected array $guarded = [];

    /**
     * @var string[]
     *
     * @psalm-var array{0: string}
     */
    protected array $fillable = [
        'name'
    ];

    public function users()
    {
        $userModel = Porteiro::modelClass('User');

        return $this->belongsToMany($userModel, 'role_user')
            ->select(app($userModel)->getTable().'.*')
            ->union($this->hasMany($userModel))->getQuery();
    }

    public function permissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Porteiro::modelClass('Permission'));
    }
}
