<?php

namespace Porteiro\Http\Policies;

use Porteiro\Contracts\User;

class UserPolicy extends BasePolicy
{

    public function __construct()
    {

        dd('Polyci: aqui foi4 ');
    }

    /**
     * Determine if the given model can be viewed by the user.
     *
     * @param \Porteiro\Contracts\User $user
     * @param $model
     *
     * @return bool
     */
    public function read(User $user, $model)
    {
        // Does this record belong to the current user?
        $current = $user->id === $model->id;

        return $current || $this->checkPermission($user, $model, 'read');
    }

    /**
     * Determine if the given model can be edited by the user.
     *
     * @param \Porteiro\Contracts\User $user
     * @param $model
     *
     * @return bool
     */
    public function edit(User $user, $model)
    {
        // Does this record belong to the current user?
        $current = $user->id === $model->id;

        return $current || $this->checkPermission($user, $model, 'edit');
    }

    /**
     * Determine if the given user can change a user a role.
     *
     * @param \Porteiro\Contracts\User $user
     * @param $model
     *
     * @return bool
     */
    public function editRoles(User $user, $model)
    {
        // Does this record belong to another user?
        $another = $user->id != $model->id;

        return $another && $user->hasPermission('edit_users');
    }
}
