<?php

namespace Porteiro\Services;

use Transmissor\Services\UserService as BaseUserService;

class UserService extends BaseUserService
{
    
    /**
     * Switch user login.
     *
     * @param int $id
     *
     * @return bool
     */
    public function switchToUser($id)
    {
        try {
            $user = $this->model->find($id);
            Session::put('original_user', Auth::id());
            Auth::login($user);

            return true;
        } catch (Exception $e) {
            throw new Exception('Error logging in as user', 1);
        }
    }


    /**
     * Switch back.
     *
     * @param int $id
     *
     * @return bool
     */
    public function switchUserBack()
    {
        try {
            $original = Session::pull('original_user');
            $user = $this->model->find($original);
            Auth::login($user);

            return true;
        } catch (Exception $e) {
            throw new Exception('Error returning to your user', 1);
        }
    }

    /**
     * Invite a new member.
     *
     * @param array $info
     */
    public function invite($info)
    {
        $password = substr(md5(rand(1111, 9999)), 0, 10);

        return DB::transaction(
            function () use ($password, $info) {
                $user = $this->model->create(
                    [
                    'email' => $info['email'],
                    'name' => $info['name'],
                    'password' => bcrypt($password),
                    ]
                );

                return $this->create($user, $password, $info['roles'], true);
            }
        );
    }

    /**
     * Destroy the profile.
     *
     * @param int $id
     *
     * @return bool
     */
    public function destroy($id)
    {
        try {
            return DB::transaction(
                function () use ($id) {
                    $this->unassignAllRoles($id);

                    $userResult = $this->model->find($id)->delete();

                    return $userResult;
                }
            );
        } catch (Exception $e) {
            throw new Exception('We were unable to delete this profile', 1);
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    /**
     * Assign a role to the user.
     *
     * @param string $roleName
     * @param int    $userId
     */
    public function assignRole($roleName, $userId)
    {
        $role = $this->role->findByName($roleName);
        $user = $this->model->find($userId);

        $user->roles()->attach($role);
    }

    /**
     * Unassign a role from the user.
     *
     * @param string $roleName
     * @param int    $userId
     */
    public function unassignRole($roleName, $userId)
    {
        $role = $this->role->findByName($roleName);
        $user = $this->model->find($userId);

        $user->roles()->detach($role);
    }

    /**
     * Unassign all roles from the user.
     *
     * @param string $roleName
     * @param int    $userId
     */
    public function unassignAllRoles($userId)
    {
        $user = $this->model->find($userId);
        $user->roles()->detach();
    }
}
