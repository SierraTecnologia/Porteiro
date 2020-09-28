<?php namespace Porteiro\Models\Permissions;

use App\Models\Role;
use Pedreiro\Models\Base;

class RolePermission extends Base
{
    /**
     * The roles that belong to the permission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }

    /**
     * Get the permission object by name.
     *
     * @param  $name
     * @return mixed
     */
    public static function getByName($name)
    {
        return static::where('name', '=', $name)->first();
    }
}
