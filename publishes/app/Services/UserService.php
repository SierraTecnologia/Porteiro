<?php

namespace App\Services;

use DB;
use Auth;
use Session;
use Exception;
use App\Models\User;
use Siravel\Models\UserMeta;
use Facilitador\Models\Role;
use Siravel\Events\UserRegisteredEmail;
use Facilitador\Notifications\ActivateUserEmail;
use Illuminate\Support\Facades\Schema;
use Transmissor\Services;

use Transmissor\Services\UserService as BaseUserService;

class UserService extends BaseUserService
{

    /**
     * User Meta model.
     *
     * @var UserMeta
     */
    protected $userMeta;


    public function __construct(
        User $model,
        UserMeta $userMeta,
        Role $role
    ) {
        $this->userMeta = $userMeta;
        parent::__construct($model, $role);
    }


    /**
     * Find by the user meta activation token.
     *
     * @param string $token
     *
     * @return bool
     */
    public function findByActivationToken($token)
    {
        $userMeta = $this->userMeta->where('activation_token', $token)->first();

        if ($userMeta) {
            return $userMeta->user();
        }

        return false;
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
                    $this->userMeta->firstOrCreate(
                        [
                        'user_id' => $user->id,
                        ]
                    );

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

                    if (isset($payload['meta']['marketing']) && ($payload['meta']['marketing'] == 1 || $payload['meta']['marketing'] == 'on')) {
                        $payload['meta']['marketing'] = 1;
                    } else {
                        $payload['meta']['marketing'] = 0;
                    }

                    if (isset($payload['meta']['terms_and_cond']) && ($payload['meta']['terms_and_cond'] == 1 || $payload['meta']['terms_and_cond'] == 'on')) {
                        $payload['meta']['terms_and_cond'] = 1;
                    } else {
                        $payload['meta']['terms_and_cond'] = 0;
                    }

                    $userMetaResult = (isset($payload['meta'])) ? $user->meta->update($payload['meta']) : true;

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

                    $userMetaResult = $this->userMeta->where('user_id', $id)->delete();
                    $userResult = $this->model->find($id)->delete();

                    return $userMetaResult && $userResult;
                }
            );
        } catch (Exception $e) {
            throw new Exception('We were unable to delete this profile', 1);
        }
    }

    /**
     * Set and send the user activation token via email.
     *
     * @param void
     */
    public function setAndSendUserActivationToken($user)
    {
        $token = md5(\Illuminate\Support\Str::random(40));

        $user->meta()->update(
            [
            'activation_token' => $token,
            ]
        );

        $user->notify(new ActivateUserEmail($token));
    }

}
