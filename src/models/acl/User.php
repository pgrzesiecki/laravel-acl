<?php

namespace Signes\Acl\Model;

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

use Signes\Acl\UserInterface as SignesAclUserInterface;

class User extends \Eloquent implements SignesAclUserInterface, UserInterface, RemindableInterface
{

    use UserTrait, RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token');

    /**
     * User personal permissions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function getPermissions()
    {
        return $this->belongsToMany(
            'Signes\\Acl\\Model\\Permission',
            'acl_user_permissions',
            'user_id',
            'permission_id'
        )->withPivot('actions')->withTimestamps();
    }

    /**
     * Get user roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function getRoles()
    {
        return $this->belongsToMany(
            'Signes\\Acl\\Model\\Role',
            'acl_user_roles',
            'user_id',
            'role_id'
        )->withTimestamps();
    }

    /**
     * Get user group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getGroup()
    {
        return $this->hasOne('Signes\\Acl\\Model\\Group', 'id', 'group_id');
    }
}
