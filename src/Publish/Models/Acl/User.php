<?php

namespace App\Models\Acl;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Signes\Acl\GroupInterface;
use Signes\Acl\UserInterface as SignesAclUserInterface;

/**
 * Class User
 *
 * @package App\Models\Acl
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract, SignesAclUserInterface
{
    use Authenticatable, CanResetPassword;

    /**
     * Application namespace
     *
     * @var string
     */
    protected $namespace = "App";

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'acl_users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get user personal permissions
     *
     * @return BelongsToMany
     */
    public function getPermissions()
    {
        return $this->belongsToMany(
            "{$this->namespace}\\Models\\Acl\\Permission",
            'acl_user_permissions',
            'user_id',
            'permission_id'
        )->withPivot('actions')->withTimestamps();
    }

    /**
     * Get user roles
     *
     * @return BelongsToMany
     */
    public function getRoles()
    {
        return $this->belongsToMany(
            "{$this->namespace}\\Models\\Acl\\Role",
            'acl_user_roles',
            'user_id',
            'role_id'
        )->withTimestamps();
    }

    /**
     * Get user group
     *
     * @return HasOne
     */
    public function getGroup()
    {
        return $this->hasOne("{$this->namespace}\\Models\\Acl\\Group", 'id', 'group_id');
    }

    /**
     * Set user group
     *
     * @param GroupInterface $group
     * @return $this
     */
    public function setGroup(GroupInterface $group)
    {
        $this->group_id = $group->getAttribute('id');
        return $this;
    }
}
