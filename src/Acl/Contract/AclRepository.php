<?php

namespace Signes\Acl\Contract;

use Signes\Acl\Exception\DuplicateEntryException;

/**
 * Interface AclRepository
 *
 * @package Signes\Acl\Contract
 */
interface AclRepository
{
    /**
     * Return Guest object.
     * Guest object is taken when no user is set in acl system.
     *
     * @return UserInterface
     */
    public function getGuest();

    /**
     * Create and persist new permission.
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
     * @throws DuplicateEntryException
     */
    public function grantGroupPermission(PermissionInterface $permission, GroupInterface $group, $actions = []);

    /**
     *  Grant new permissions for user
     *
     * @param PermissionInterface $permission
     * @param UserInterface $user
     * @param array|true $actions , actions array or true, if true all actions will be granted
     * @throws DuplicateEntryException
     */
    public function grantUserPermission(PermissionInterface $permission, UserInterface $user, $actions = []);

    /**
     *  Grant new permissions for group
     *
     * @param PermissionInterface $permission
     * @param RoleInterface $role
     * @param array $actions , actions array or true, if true all actions will be granted
     * @throws DuplicateEntryException
     */
    public function grantRolePermission(PermissionInterface $permission, RoleInterface $role, $actions = []);

    /**
     *  Grant new role for user
     *
     * @param RoleInterface $role
     * @param UserInterface $user
     * @throws DuplicateEntryException
     */
    public function grantUserRole(RoleInterface $role, UserInterface $user);

    /**
     * Grant new role for group
     *
     * @param RoleInterface $role
     * @param GroupInterface $group
     * @throws DuplicateEntryException
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

    /**
     * Get permission for given object.
     *
     * @param HavingPermissionsInterface $object
     * @return \Traversable
     */
    public function getPermissionsFor(HavingPermissionsInterface $object);

    /**
     * Get roles for given object.
     *
     * @param HavingRolesInterface $object
     * @return \Traversable
     */
    public function getRolesFor(HavingRolesInterface $object);
}
