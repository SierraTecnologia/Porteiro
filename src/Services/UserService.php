<?php

namespace Porteiro\Services;

use App\Models\Role;
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

    public function __construct(
        User $model,
        Role $role
    ) {
        $this->model = $model;
        $this->role = $role;
    }

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
     * Search the users.
     *
     * @param string $input
     *
     * @return mixed
     */
    public function search($input)
    {
        $query = $this->model->orderBy('created_at', 'desc');

        $columns = Schema::getColumnListing('users');

        foreach ($columns as $attribute) {
            $query->orWhere($attribute, 'LIKE', '%'.$input.'%');
        }

        return $query->paginate(env('PAGINATE', 25));
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     *
     * @return User
     */
    public function findByEmail($email)
    {
        return $this->model->findByEmail($email);
    }

    /**
     * Find by Role ID.
     *
     * @param int $id
     *
     * @return Collection
     */
    public function findByRoleID($id)
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
