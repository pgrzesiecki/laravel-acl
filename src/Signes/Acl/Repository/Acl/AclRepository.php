<?php

namespace Signes\Acl\Repository;

use Signes\Acl\GroupInterface;
use Signes\Acl\PermissionInterface;
use Signes\Acl\UserInterface;

interface AclRepository
{

    public function getGuest();

    public function createPermission($area, $permission, $actions = null, $description = '');

    public function deletePermission($area, $permission = null, $actions = null);

    public function grantGroupPermission(PermissionInterface $permission, GroupInterface $group, $actions = array());

    public function revokeGroupPermission(PermissionInterface $permission, GroupInterface $group);

    public function grantUserPermission(PermissionInterface $permission, UserInterface $user, $actions = array());

    public function revokeUserPermission(PermissionInterface $permission, UserInterface $user);
}
