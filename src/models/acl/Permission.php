<?php

namespace Signes\Acl\Model;

use Signes\Acl\PermissionInterface;

class Permission extends \Eloquent implements PermissionInterface
{

    /**
     * Mass fillable columns
     *
     * @var array
     */
    protected $fillable = array('area', 'permission', 'actions', 'description');

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'acl_permissions';

    public function getAreaPermission($area, $permission)
    {

    }
}
