<?php

namespace Signes\Acl\Model;

use Signes\Acl\PermissionInterface;

class Permission extends \Eloquent implements PermissionInterface
{

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
