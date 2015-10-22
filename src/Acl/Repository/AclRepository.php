<?php

namespace Signes\Acl\Repository;

use Signes\Acl\Exception\DuplicateEntry;
use Signes\Acl\GroupInterface;
use Signes\Acl\PermissionInterface;
use Signes\Acl\RoleInterface;
use Signes\Acl\UserInterface;

/**
 * Interface AclRepository
 *
 * @package    Signes\Acl
 * @subpackage Signes\Acl\Repository
 */
interface AclRepository
{
    /**
     * Return Guest object
     *
     * @return UserInterface
     */
    public function getGuest();

    /**
     * Create new permission
     *
     * @param string $area        ,area name
     * @param string $permission  , permission name
     * @param array|null $actions , permission actions
     * @param string $description , permission description
     * @return false|PermissionInterface
     */
    public function createPermission($area, $permission, $actions = null, $description = '');

    /**
     * Delete permission from base.
     * We can remove whole zone, zone with permission, or one specific action.
     *
     * @param string $area               , are name
     * @param string|null $permission    , permission name
     * @param array|string|null $actions , action(s) name
     * @return false|int , number of removed items or false is error occurred
     */
    public function deletePermission($area, $permission = null, $actions = null);

    /**
     *  Grant new permissions for group
     *
     * @param PermissionInterface $permission
     * @param GroupInterface $group
     * @param array $actions , actions array or true, if true all actions will be granted
     * @throws DuplicateEntry
     */
    public function grantGroupPermission(PermissionInterface $permission, GroupInterface $group, $actions = []);

    /**
     *  Grant new permissions for user
     *
     * @param PermissionInterface $permission
     * @param UserInterface $user
     * @param array|true $actions , actions array or true, if true all actions will be granted
     * @throws DuplicateEntry
     */
    public function grantUserPermission(PermissionInterface $permission, UserInterface $user, $actions = []);

    /**
     *  Grant new permissions for group
     *
     * @param PermissionInterface $permission
     * @param RoleInterface $role
     * @param array $actions , actions array or true, if true all actions will be granted
     * @throws DuplicateEntry
     */
    public function grantRolePermission(PermissionInterface $permission, RoleInterface $role, $actions = []);

    /**
     *  Grant new role for user
     *
     * @param RoleInterface $role
     * @param UserInterface $user
     * @throws DuplicateEntry
     */
    public function grantUserRole(RoleInterface $role, UserInterface $user);

    /**
     * Grant new role for group
     *
     * @param RoleInterface $role
     * @param GroupInterface $group
     * @throws DuplicateEntry
     */
    public function grantGroupRole(RoleInterface $role, GroupInterface $group);

    /**
     * Revoke group permissions
     *
     * @param PermissionInterface $permission
     * @param GroupInterface $group
     * @return bool
     */
    public function revokeGroupPermission(PermissionInterface $permission, GroupInterface $group);

    /**
     * Revoke user permission
     *
     * @param PermissionInterface $permission
     * @param UserInterface $user
     * @return bool
     */
    public function revokeUserPermission(PermissionInterface $permission, UserInterface $user);

    /**
     * Revoke role permissions
     *
     * @param PermissionInterface $permission
     * @param RoleInterface $role
     * @return bool
     */
    public function revokeRolePermission(PermissionInterface $permission, RoleInterface $role);

    /**
     * Revoke User Role
     *
     * @param RoleInterface $role
     * @param UserInterface $user
     * @return bool
     */
    public function revokeUserRole(RoleInterface $role, UserInterface $user);


    /**
     * Revoke Group Role
     *
     * @param RoleInterface $role
     * @param GroupInterface $group
     * @return bool
     */
    public function revokeGroupRole(RoleInterface $role, GroupInterface $group);

    /**
     * Set different site namespace
     *
     * @param string $namespace , new namespace
     */
    public function setSiteNamespace($namespace);
}
