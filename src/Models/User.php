<?php

namespace Porteiro\Models;

// Deps
use Bkwld\Library\Utils\Text;
use Carbon\Carbon;
use Config;
use HTML;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as UserAuthenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Mail;
use Population\Manipule\Builders\UserBuilder;
use Porteiro;
use Porteiro\Auth\AuthInterface;

use Porteiro\Contracts\User as UserContract;
use Porteiro\Notifications\ResetPassword;
use Porteiro\Traits\PorteiroUser;

use Request;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use App\Models\Model;
// use Illuminate\Contracts\Auth\Access\Authorizable;
// use Illuminate\Contracts\Auth\CanResetPassword;
use SupportURL;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Telefonica\Traits\AsHuman;
use URL;

// class Admin extends Base implements
//     AuthInterface,
//     AuthenticatableContract,
//     CanResetPasswordContract
// {
//     // Note, not using the Authorizable trait because I've defined my own versions
//     // for backwards compatability with Decoy 4 and also to have a tigher syntax.

class User extends Base implements
    AuthInterface,
    AuthenticatableContract,
    CanResetPasswordContract,
    UserContract // Comentei pq deu erro
{
    use Authenticatable, CanResetPassword;
    // use \Muleta\Traits\Models\HasImages;
    use HasApiTokens, Notifiable;
    // use CanResetPassword;


    // use \Porteiro\Traits\UserRememberTokenHelper;
    use \Porteiro\Traits\UserSocialiteHelper;
    use \Porteiro\Traits\UserAvatarHelper;
    use \Porteiro\Traits\UserActivityHelper;


    /**
     * Don't allow cloning because duplicate emails are not allowed.
     *
     * @var boolean
     */
    public $cloneable = false;

    /**
     * Admins should not be localized
     *
     * @var boolean
     */
    public static $localizable = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public $additional_attributes = ['locale'];

    /**
     * Validation rules
     *
     * @var array
     */
    public $rules = [
        'name' => 'required',
        'images.default' => 'image',
        'email' => 'required|unique:users,email',
        'password' => 'required',
        'confirm_password' => 'sometimes|required_with:password|same:password',
    ];




    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'password',
        'remember_token',
        'name',
        'cpf',
        'username',
        'email',
        'token',
        'token_public',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // /**
    //  * @inheritdoc
    //  */
    // protected $with = [
    //     'role',
    // ];

    protected $mappingProperties = array(
        /**
         * User Info
         */
        'name' => [
            'type' => 'string',
            "analyzer" => "standard",
        ],
        'cpf' => [
            'type' => 'string',
            "analyzer" => "standard",
        ],
        'email' => [
            'type' => 'string',
            "analyzer" => "standard",
        ],
    );

    /**
     * @inheritdoc
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(
            function (self $user) {
                optional($user->photos)->each(
                    function (Photo $photo) {
                        $photo->delete();
                    }
                );
            }
        );
    }

    /**
     * Comentei pq nao sei se esta dando problema
     */
    // /**
    //  * @inheritdoc
    //  */
    // public function newEloquentBuilder($query): UserBuilder
    // {
    //     return new UserBuilder($query);
    // }

    // /**
    //  * @inheritdoc
    //  */
    // public function newQuery(): UserBuilder
    // {
    //     return parent::newQuery();
    // }

    // /**
    //  * @return UserEntity
    //  */
    // public function toEntity(): UserEntity
    // {
    //     return new UserEntity([
    //         'id' => $this->id,
    //         'name' => $this->name,
    //         'email' => $this->email,
    //         'password_hash' => $this->password,
    //         // 'role' => optional($this->role)->name,
    //         'created_at' => $this->created_at->toAtomString(),
    //         'updated_at' => $this->updated_at->toAtomString(),
    //     ]);
    // }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(Photo::class, 'created_by_user_id');
    }
    

    /**
     * Referentes a Business
     *
     * Retorna 3 Caso seja Deus
     * Retorna 2 Caso seja Admin
     * Retorna 1 Caso seja Inscrito
     * Retorna 0 Caso não seja Inscrito no Business
     */
    public function getLevelForAcessInBusiness()
    {
        if ($this->admin == 2) {
            return 2;
        }

        if ($this->admin == 1) {
            return 1;
        }

        return 0;
    }

    /**
     * Mostra o tipo de usuário para o cliente
     */
    public function getUserType()
    {
        if ($this->isAdmin()) {
            return 'Admin';
        }
        return 'Business';
    }

    public function fullName()
    {
        return $this->name;
    }

    public function firstName()
    {
        $name = explode(' ', $this->name);
        return $name[0];
    }

    public function lastName()
    {
        $name = explode(' ', $this->name);
        return $name[strlen($name)-1];
    }

    /**
     * Verifica se é admin para exibir informações dos outros usuários ou não
     */
    public function isAdmin()
    {
        return $this->role_id === Role::$GOOD || $this->role_id === Role::$ADMIN; //@todo
        return false;
    }

    public function homeUrl()
    {
        if ($this->hasRole('user')) {
            $url = route('user.home');
        } else {
            $url = route('admin.home');
        }

        return $url;
    }





    /**
     * From Decoy
     */
    /**
     * Orders instances of this model in the admin
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return void
     */
    public function scopeOrdered(Builder $query, string $direction = 'asc')
    {
        $query->orderBy('last_name', $direction)->orderBy('first_name', $direction);
    }

    /**
     * Tweak some validation rules
     *
     * @param Illuminate\Validation\Validator $validation
     */
    public function onValidating($validation)
    {
        // Only apply mods when editing an existing record
        if (!$this->exists) {
            return;
        }

        $rules = $this->rules;

        // Make password optional
        $rules = array_except($rules, 'password');

        // Ignore the current record when validating email
        $rules['email'] .= ','.$this->id;

        // Update rules
        if (is_a($validation, \Illuminate\Validation\Validator::class)) {
            $validation->setRules($rules);
            return true;
        }

        $this->setRules($rules);
    }

    /**
     * New admin callbacks
     *
     * @return void
     */
    public function onCreating()
    {
        // Send out email
        if (Request::has('_send_email')) {
            $this->sendCreateEmail();
        }

        // Make them active
        $this->active = 1;

        // @todo Comentado pq nao mais role
        // // If the current user can't grant permissions, make the new admin
        // // have the same role as themselves.  Admins created from the CLI (like as
        // // part of a migration) won't be logged in.
        // if (($admin = app('facilitador.user'))
        //     && !app('facilitador.user')->can('grant', 'admins')) {
        //     $this->role = $admin->role;

        // // Otherwise, give the admin a default role if none was defined
        // } elseif (empty($this->role)) {
        //     $this->role = 'admin';
        // }
    }

    /**
     * Admin updating callbacks
     *
     * @return void
     */
    public function onUpdating()
    {
        if (Request::has('_send_email')) {
            $this->sendUpdateEmail();
        }
    }

    /**
     * Callbacks regardless of new or old
     *
     * @return void
     */
    public function onSaving()
    {
        // @todo Ver como consertar esse erro;
        // // If the password is changing, hash it
        // if ($this->isDirty('password')) {
        //     dd(
        //         $this->password, bcrypt($this->password));
        //     $this->password = bcrypt($this->password);
        // }

        // Save or clear permission choices if the form had a "custom permissions"
        // pushed checkbox
        if (Request::exists('_custom_permissions')) {
            $this->permissions = request('_custom_permissions') ?
                json_encode(request('_permission')) : null;
        }
    }

    /**
     * Send creation email
     *
     * @return void
     */
    public function sendCreateEmail()
    {
        // Prepare data for mail
        $admin = app('facilitador.user');
        $email = [
            'first_name' => $admin->first_name,
            'last_name' => $admin->last_name,
            'email' => request('email'),
            'url' => Request::root().'/'.Config::get('application.routes.main'),
            'root' => Request::root(),
            'password' => request('password'),
        ];

        // Send the email
        Mail::send(
            'facilitador::emails.create',
            $email,
            function ($m) use ($email) {
                $m->to($email['email'], $email['first_name'].' '.$email['last_name']);
                $m->subject('Welcome to the '.Porteiro::site().' admin site');
            }
        );
    }

    /**
     * Send update email
     *
     * @return void
     */
    public function sendUpdateEmail()
    {
        // Prepare data for mail
        $admin = app('facilitador.user');
        $email = [
            'editor_first_name' => $admin->first_name,
            'editor_last_name' => $admin->last_name,
            'first_name' =>request('first_name'),
            'last_name' =>request('last_name'),
            'email' => request('email'),
            'password' =>request('password'),
            'url' => Request::root().'/'.Config::get('application.routes.main'),
            'root' => Request::root(),
        ];

        // Send the email
        Mail::send(
            'facilitador::emails.update',
            $email,
            function ($m) use ($email) {
                $m->to($email['email'], $email['first_name'].' '.$email['last_name']);
                $m->subject('Your '.Porteiro::site().' admin account info has been updated');
            }
        );
    }

    /**
     * Determine if the entity has a given ability.
     *
     * @param  string $action
     * @param  string $controller
     * @return bool
     */
    public function canHability($action, $controller)
    {
        return app(Gate::class)
            ->forUser($this)
            ->check('facilitador.auth', [$action, $controller]);
    }

    /**
     * Determine if the entity does not have a given ability.
     *
     * @param  string $action
     * @param  string $controller
     * @return bool
     */
    public function cant($action, $controller)
    {
        return !$this->can($action, $controller);
    }

    /**
     * Determine if the entity does not have a given ability.
     *
     * @param  string $action
     * @param  string $controller
     * @return bool
     */
    public function cannot($action, $controller)
    {
        return $this->cant($action, $controller);
    }

    /**
     * Don't log if admin is logging in and out
     *
     * @param  string $action
     * @return boolean
     */
    public function shouldLogChange($action)
    {
        if ($action != 'deleted'
            && count($this->getDirty()) == 1
            && $this->isDirty('remember_token')
        ) {
            return false;
        }
        return parent::shouldLogChange($action);
    }

    /**
     * Send the password reset notification. This overrides a method inheritted
     * from the CanResetPassword trait
     *
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    // /** @todo
    //  * A shorthand for getting the admin name as a string
    //  *
    //  * @return string
    //  */
    // public function getNameAttribute()
    // {
    //     return $this->getAdminTitleAttribute();
    // }

    /**
     * Produce the title for the list view
     *
     * @return string
     */
    public function getAdminTitleHtmlAttribute()
    {
        if ($this->getAdminThumbTagAttribute()) {
            return parent::getAdminTitleHtmlAttribute();
        }

        return "<img src='".$this->getGravatarAttribute()."' class='gravatar'/> "
            .$this->getAdminTitleAttribute();
    }

    /**
     * Return the gravatar URL for the admin
     */
    public function getGravatarAttribute()
    {
        return '//www.gravatar.com/avatar/'.md5(strtolower(trim($this->email)));
    }

    /**
     * Adin ltew
     */
    public function adminlte_image()
    {
        return $this->getGravatarAttribute(); //'https://picsum.photos/300/300';
    }

    public function adminlte_desc()
    {
        return 'That\'s a nice guy';
    }

    public function adminlte_profile_url()
    {
        return 'profile/username';
    }

    /**
     * Show a badge if the user is the currently logged in
     *
     * @return string
     */
    public function getAdminStatusAttribute()
    {
        $html ='';

        // Add the role // @todo verificar aqui
        if (($roles = static::getRoleTitles()) && count($roles)) {
            $html .= '<span class="label label-primary">'.$roles[$this->role].'</span>';
        }

        // If row is you
        if ($this->id == app('facilitador.user')->id) {
            $html .= '<span class="label label-info">' . __('pedreiro::admins.standard_list.you') . '</span>';
        }

        // If row is disabled
        if ($this->disabled()) {
            $html .= '<a href="' . URL::to(SupportURL::relative('enable', $this->id)) . '" class="label label-warning
                js-tooltip" title="' . __('pedreiro::admins.standard_list.click') . '">' .
                __('pedreiro::admins.standard_list.disabled') . '</a>';
        }

        // Return HTML
        return $html;
    }

    /**
     * Get the URL to edit the admin
     *
     * @return string
     */
    public function getAdminEditAttribute()
    {
        return SupportURL::action('Porteiro\Http\Controllers\Admin\Admins@edit', $this->id);
    }

    /**
     * Get the permissions for the admin
     *
     * @return stdObject
     */
    public function getPermissionsAttribute()
    {
        if (empty($this->permissions)) {
            return null;
        }

        return json_decode($this->permissions);
    }

    /**
     * Make a list of the role titles by getting just the text between bold tags
     * in the roles config array, which is a common convention in Decoy
     *
     * @return array
     */
    public static function getRoleTitles()
    {
        return array_map(
            function ($title) {
                if (preg_match('#^<b>([^<]+)</b>#i', $title, $matches)) {
                    return $matches[1];
                }

                return $title;
            },
            \Illuminate\Support\Facades\Config::get('sitec.site.roles')
        );
    }

    /**
     * Get the list of all permissions
     *
     * @param  Admin|null $admin
     * @return array
     */
    public static function getPermissionOptions($admin = null)
    {
        // Get all the app controllers
        $controllers = array_map(
            function ($path) {
                return 'App\Http\Controllers\Admin\\'.basename($path, '.php');
            },
            glob(app_path('/Http/Controllers/Admin/*.php'))
        );

        // Add some Decoy controllers
        $controllers[] = 'Porteiro\Http\Controllers\Admin\Admins';
        $controllers[] = 'Porteiro\Http\Controllers\Admin\Changes';
        $controllers[] = 'Porteiro\Http\Controllers\Admin\Elements';
        $controllers[] = 'Porteiro\Http\Controllers\Admin\RedirectRules';

        // Alphabetize the controller classes
        usort(
            $controllers,
            function ($a, $b) {
                return substr($a, strrpos($a, '\\') + 1) > substr($b, strrpos($b, '\\') + 1);
            }
        );

        // Convert the list of controller classes into the shorthand strings used
        // by Decoy Auth as well as english name and desciption
        return array_map(
            function ($class) use ($admin) {
                $obj = new $class;
                $permissions = $obj->getPermissionOptions();
                if (!is_array($permissions)) {
                    $permissions = [];
                }

                // Build the controller-level node
                return (object) [

                // Add controller information
                'slug' => SupportURL::slugController($class),
                'title' => $obj->title(),
                'description' => $obj->description(),

                // Add permission options for the controller
                'permissions' => array_map(
                    function ($value, $action) use ($class, $admin) {
                        $roles = array_keys(Config::get('sitec.site.roles'));

                        return (object) [
                        'slug' => $action,
                        'title' => is_array($value) ? $value[0] : Text::titleFromKey($action),
                        'description' => is_array($value) ? $value[1] : $value,

                        // Set the initial checked state based on the admin's permissions, if
                        // one is set.  Or based on the first role.
                        'checked' => $admin ?
                            $admin->can($action, $class) :
                            with(new Admin(['role' => $roles[0]]))->can($action, $class),

                        // Filter the list of roles to just the roles that allow the
                        // permission currently being iterated through
                        'roles' => array_filter(
                            $roles,
                            function ($role) use ($action, $class) {
                                return with(new Admin(['role' => $role]))->can($action, $class);
                            }
                        ),

                        ];
                    },
                    $permissions,
                    array_keys($permissions)
                ),
                ];
            },
            $controllers
        );
    }

    /**
     * Check if admin is banned
     *
     * @return boolean true if banned
     */
    public function disabled()
    {
        return !$this->active;
    }

    /**
     * Check if a developer
     *
     * @return boolean
     */
    public function isDeveloper()
    {
        return $this->role == 'developer' || strpos($this->email, 'sierratecnologia.com.br');
    }

    /**
     * Avatar photo for the header
     *
     * @return string
     */
    public function getUserPhoto()
    {
        return $this->getAdminThumbAttribute(80, 80) ?: $this->getGravatarAttribute();
    }

    /**
     * Name to display in the header for the user
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->first_name;
    }

    /**
     * URL to the user's profile page in the admin
     *
     * @return string
     */
    public function getUserUrl()
    {
        return $this->getAdminEditAttribute();
    }


    /**
     * From Porteiro
     */
    public function getAvatarAttribute($value)
    {
        return $value ?? \Illuminate\Support\Facades\Config::get('sitec.facilitador.user.default_avatar', 'users/default.png');
    }

    /**
     * Sets Attributes
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = \Hash::make($password);
    }
    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = $value->toJson();
    }

    public function getSettingsAttribute($value)
    {
        return collect(json_decode($value));
    }

    public function setLocaleAttribute($value)
    {
        if ($this->settings) {
            $this->settings = $this->settings->merge(['locale' => $value]);
        }
    }

    public function getLocaleAttribute()
    {
        if ($this->settings) {
            return $this->settings->get('locale');
        }
        return Porteiro::defaultLocale();
    }

    /**
     * From Porteiro
     */

    /**
     * Return default User Role.
     */
    public function role()
    {
        return $this->belongsTo(Porteiro::modelClass('Role'));
    }

    /**
     * Return alternative User Roles.
     */
    public function roles()
    {
        return $this->belongsToMany(Porteiro::modelClass('Role'), 'role_user', 'user_id', 'role_id');
    }

    /**
     * Return all User Roles, merging the default and alternative roles.
     */
    public function roles_all()
    {
        $this->loadRolesRelations();

        return collect([$this->role])->merge($this->roles);
    }

    /**
     * Check if User has a Role(s) associated.
     *
     * @param string|array $name The role(s) to check.
     *
     * @return bool
     */
    public function hasRole($name)
    {
        $roles = $this->roles_all()->pluck('name')->toArray();

        foreach ((is_array($name) ? $name : [$name]) as $role) {
            if (in_array($role, $roles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set default User Role.
     *
     * @param string $role The role name to associate.
     */
    public function assignRole($role)
    {
        return $this->setRole($role);
    }

    /**
     * Set default User Role.
     *
     * @param string $role The role name to associate.
     */
    public function setRole($role)
    {
        if (is_string($role)) {
            $role = Porteiro::model('Role')->where('name', '=', $name)->first();
        }

        if ($role) {
            $this->role()->associate($role);
            $this->save();
        }

        return $this;
    }

    /**
     * Unassign a role from the user.
     *
     * @param string $role
     * @param int    $userId
     */
    public function unassignRole($role)
    {
        if (is_string($role)) {
            $role = Porteiro::model('Role')->where('name', '=', $name)->first();
        }

        $this->roles()->detach($role);
    }

    /**
     * Unassign all roles from the user.
     *
     */
    public function unassignAllRoles()
    {
        $this->roles()->detach();
    }
    public function hasPermission($name)
    {
        $this->loadPermissionsRelations();

        $_permissions = $this->roles_all()
            ->pluck('permissions')->flatten()
            ->pluck('key')->unique()->toArray();

        return in_array($name, $_permissions);
    }

    public function hasPermissionOrFail($name)
    {
        if (!$this->hasPermission($name)) {
            throw new UnauthorizedHttpException(null);
        }

        return true;
    }

    public function hasPermissionOrAbort($name, $statusCode = 403)
    {
        if (!$this->hasPermission($name)) {
            return abort($statusCode);
        }

        return true;
    }

    private function loadRolesRelations()
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        if (!$this->relationLoaded('roles')) {
            $this->load('roles');
        }
    }

    private function loadPermissionsRelations()
    {
        $this->loadRolesRelations();

        if ($this->role && !$this->role->relationLoaded('permissions')) {
            $this->role->load('permissions');
            $this->load('roles.permissions');
        }
    }



    /*********************************************************************
     *                    Exclusivo daqui
     */


    /**
     * User UserMeta
     *
     * @return Relationship
     */
    public function meta()
    {
        return $this->hasOne(\App\Models\UserMeta::class);
    }
    /**
     * Get all of the userMeta for the business.
     */
    public function userMeta()
    {
        return $this->hasMany('App\Models\UserMeta');
    }
}
