<?php

namespace Signes\Acl\Repository;

use Signes\Acl\GroupInterface;
use Signes\Acl\PermissionInterface;
use Signes\Acl\RoleInterface;
use Signes\Acl\UserInterface;

interface AclRepository
{
    public function getGuest();

    public function createPermission($area, $permission, $actions = null, $description = '');

    public function deletePermission($area, $permission = null, $actions = null);

    public function grantGroupPermission(PermissionInterface $permission, GroupInterface $group, $actions = array());

    public function grantUserPermission(PermissionInterface $permission, UserInterface $user, $actions = array());

    public function grantRolePermission(PermissionInterface $permission, RoleInterface $role, $actions = array());

    public function grantUserRole(RoleInterface $role, UserInterface $user);

    public function grantGroupRole(RoleInterface $role, GroupInterface $group);

    public function revokeGroupPermission(PermissionInterface $permission, GroupInterface $group);

    public function revokeUserPermission(PermissionInterface $permission, UserInterface $user);

    public function revokeRolePermission(PermissionInterface $permission, RoleInterface $role);

    public function revokeUserRole(RoleInterface $role, UserInterface $user);

    public function revokeGroupRole(RoleInterface $role, GroupInterface $group);
}
