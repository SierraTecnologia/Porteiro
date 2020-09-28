<?php 

namespace Porteiro\Models\Permissions;

use Pedreiro\Models\Base;

class EntityPermission extends Base
{

    /**
     * @var string[]
     *
     * @psalm-var array{0: string, 1: string}
     */
    protected $fillable = ['role_id', 'action'];

    /**
     * @var false
     */
    public $timestamps = false;

    /**
     * Get all this restriction's attached entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function restrictable()
    {
        return $this->morphTo('restrictable');
    }
}
