<?php

namespace Signes\Acl\Model;

use Signes\Acl\GroupInterface;

class Group extends \Eloquent implements GroupInterface
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'acl_groups';

    /**
     * User group permissions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function getPermissions()
    {
        return $this->belongsToMany(
            'Signes\\Acl\\Model\\Permission',
            'acl_group_permissions',
            'group_id',
            'permission_id'
        )->withPivot('actions')->withTimestamps();
    }

    /**
     * Get group roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function getRoles()
    {
        return $this->belongsToMany(
            'Signes\\Acl\\Model\\Role',
            'acl_group_roles',
            'group_id',
            'role_id'
        )->withTimestamps();
    }
}
