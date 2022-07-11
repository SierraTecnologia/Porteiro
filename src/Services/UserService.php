<?php

namespace Porteiro\Services;

use Porteiro\Models\Role;
use App\Models\User;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Facades\Schema;
use Session;
use Siravel\Events\UserRegisteredEmail;

class UserService
{
    
    /**
     * User model.
     *
     * @var User
     */
    public $model;


    /**
     * Role Service.
     *
     * @var RoleService
     */
    protected $role;

    /**
     * Get all users.
     *
     * @return array
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Find a user.
     *
     * @param int $id
     *
     * @return User
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Find by Role ID.
     *
     * @param int $id
     *
     * @return array
     *
     * @psalm-return list<mixed>
     */
    public function findByRoleID($id): array
    {
        $usersWithRepo = [];
        $users = $this->model->all();

        foreach ($users as $user) {
            if ($user->roles->first()->id == $id) {
                $usersWithRepo[] = $user;
            }
        }

        return $usersWithRepo;
    }


    /**
     * Create a user's profile.
     *
     * @param User   $user      User
     * @param string $password  the user password
     * @param string $role      the role of this user
     * @param bool   $sendEmail Whether to send the email or not
     *
     * @return User
     */
    public function create($user, $password, $role = 'member', $sendEmail = true)
    {
        try {
            DB::transaction(
                function () use ($user, $password, $role, $sendEmail) {
                    $this->assignRole($role, $user->id);

                    if ($sendEmail) {
                        event(new UserRegisteredEmail($user, $password));
                    }
                }
            );

            $this->setAndSendUserActivationToken($user);

            return $user;
        } catch (Exception $e) {
            throw new Exception('We were unable to generate your profile, please try again later.', 1);
        }
    }

    /**
     * Update a user's profile.
     *
     * @param int   $userId User Id
     * @param array $inputs UserMeta info
     *
     * @return User
     */
    public function update($userId, $payload)
    {
        if (isset($payload['meta']) && !isset($payload['meta']['terms_and_cond'])) {
            throw new Exception('You must agree to the terms and conditions.', 1);
        }

        try {
            return DB::transaction(
                function () use ($userId, $payload) {
                    $user = $this->model->find($userId);

                    $user->update($payload);

                    if (isset($payload['roles'])) {
                        $this->unassignAllRoles($userId);
                        $this->assignRole($payload['roles'], $userId);
                    }

                    return $user;
                }
            );
        } catch (Exception $e) {
            throw new Exception('We were unable to update your profile', 1);
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
     *
     * @return void
     */
    public function assignRole($roleName, $userId): void
    {
        $role = $this->role->findByName($roleName);
        $user = $this->model->find($userId);

        $user->roles()->attach($role);
    }

    /**
     * Unassign all roles from the user.
     *
     * @param string $roleName
     * @param int    $userId
     *
     * @return void
     */
    public function unassignAllRoles($userId): void
    {
        $user = $this->model->find($userId);
        $user->roles()->detach();
    }
}
