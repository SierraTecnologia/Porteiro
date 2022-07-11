<?php

namespace Porteiro\Services;

use Auth;
use Config;
use DB;
use Exception;
use Illuminate\Support\Facades\Schema;
use Porteiro\Models\Role;
use Porteiro\Services\UserService;

class RoleService
{


    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    */


    /**
     * All roles
     *
     * @return \Illuminate\Support\Collection|null|static|Role
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Find a role
     *
     * @param  integer $id
     * @return \Illuminate\Support\Collection|null|static|Role
     */
    public function find($id)
    {
        return $this->model->find($id);
    }


    /**
     * Find Role by name
     *
     * @param string $name
     *
     * @return \Illuminate\Support\Collection|null|static|Role
     */
    public function findByName($name)
    {
        return $this->model->where('name', $name)->firstOrFail();
    }

    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    */

    /**
     * Create a role
     *
     * @param  array $input
     * @return Role
     */
    public function create($input)
    {
        try {
            if (isset($input['permissions'])) {
                $input['permissions'] = implode(',', array_keys($input['permissions']));
            } else {
                $input['permissions'] = null;
            }
            return $this->model->create($input);
        } catch (Exception $e) {
            throw new Exception("Failed to create role", 1);
        }
    }

    /**
     * Update a role
     *
     * @param  int   $id
     * @param  array $input
     * @return boolean
     */
    public function update($id, $input)
    {
        if (isset($input['permissions'])) {
            $input['permissions'] = implode(',', array_keys($input['permissions']));
        } else {
            $input['permissions'] = null;
        }

        $role = $this->model->find($id);
        $role->update($input);

        return $role;
    }
}
