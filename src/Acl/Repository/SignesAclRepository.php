<?php

namespace Signes\Acl\Repository;

use App\Models\Acl\Permission;
use Signes\Acl\Exception\DuplicateEntry;
use Signes\Acl\GroupInterface;
use Signes\Acl\PermissionInterface;
use Signes\Acl\RoleInterface;
use Signes\Acl\UserInterface;

/**
 * Class SignesAclRepository
 *
 * @package    Signes\Acl
 * @subpackage Signes\Acl\Repository
 */
class SignesAclRepository implements AclRepository
{

    /**
     * Application namespace
     *
     * @var]
     */
    protected $appNamespace = "App";

    /**
     * Get model with correct site namespace
     *
     * @param $model
     * @return mixed
     */
    protected function getModelName($model)
    {
        $model = "{$this->appNamespace}\\Models\\Acl\\{$model}";
        return new $model;
    }

    /**
     * Set different site namespace
     *
     * @param $namespace
     */
    public function setSiteNamespace($namespace)
    {
        $this->appNamespace = (string) $namespace;
    }

    /**
     * Get Guest object
     *
     * @return mixed
     */
    public function getGuest()
    {
        return $this->getModelName('User')->find(1);
    }

    /**
     * Create new permission
     *
     * @param $area
     * @param $permission
     * @param null $actions
     * @param string $description
     * @return Permission
     */
    public function createPermission($area, $permission, $actions = null, $description = '')
    {
        $permission_exists = $this->getModelName('Permission')
            ->where('area', '=', $area)
            ->where('permission', '=', $permission)
            ->first();

        if (!$permission_exists) {
            $new_permission = $this->getModelName('Permission');
            $new_permission->area = $area;
            $new_permission->permission = $permission;
            $new_permission->actions = ($actions !== null) ? serialize($actions) : null;
            $new_permission->description = $description;
            $new_permission->save();

            return $new_permission;

        }

        return false;
    }

    /**
     * Delete permission from base.
     * We can remove whole zone, zone with permission, or one specific action.
     *
     * @param $area
     * @param null $permission
     * @param null $actions
     * @return bool
     */
    public function deletePermission($area, $permission = null, $actions = null)
    {

        /**
         * Delete area
         */
        if ($permission === null && $actions === null) {
            return $this->getModelName('Permission')->where('area', '=', $area)->delete();
        }

        /**
         * Delete area and zone
         */
        if (is_string($permission) && $actions === null) {
            return $this->getModelName('Permission')
                ->where('area', '=', $area)
                ->where('permission', '=', $permission)->delete();
        }

        /**
         * Keep row in database, but remove actions from array
         */
        if (is_string($permission) && $actions !== null) {
            $actions = !is_array($actions) ? array($actions) : $actions;
            $permission = $this->getModelName('Permission')
                ->where('area', '=', $area)
                ->where('permission', '=', $permission)->first();

            if ($permission) {
                $currentActions = unserialize($permission->actions);
                if (is_array($currentActions)) {
                    foreach ($actions as $action) {
                        if (($key = array_search($action, $currentActions)) !== false) {
                            unset($currentActions[$key]);
                        }
                    }

                    $permission->actions = serialize($currentActions);
                    return $permission->save();
                }
            }
        }

        return false;
    }

    /**
     *  Grant new permissions for user
     *
     * @param PermissionInterface $permission
     * @param UserInterface $user
     * @param array $actions , actions array or true, if true all actions will be granted
     * @throws DuplicateEntry
     */
    public function grantUserPermission(PermissionInterface $permission, UserInterface $user, $actions = array())
    {
        try {
            $actions = ($actions === true) ? serialize($permission->getAttribute('actions')) : serialize($actions);
            $user->getPermissions()->save($permission, array('actions' => $actions));
        } catch (\Exception $e) {
            throw new DuplicateEntry($e->getMessage());
        }
    }

    /**
     *  Grant new permissions for group
     *
     * @param PermissionInterface $permission
     * @param GroupInterface $group
     * @param array $actions , actions array or true, if true all actions will be granted
     * @throws DuplicateEntry
     */
    public function grantGroupPermission(PermissionInterface $permission, GroupInterface $group, $actions = array())
    {
        try {
            $actions = ($actions === true) ? $permission->getAttribute('actions') : serialize($actions);
            $group->getPermissions()->save($permission, array('actions' => $actions));
        } catch (\Exception $e) {
            throw new DuplicateEntry($e->getMessage());
        }
    }


    /**
     *  Grant new permissions for group
     *
     * @param PermissionInterface $permission
     * @param RoleInterface $role
     * @param array $actions , actions array or true, if true all actions will be granted
     * @throws DuplicateEntry
     */
    public function grantRolePermission(PermissionInterface $permission, RoleInterface $role, $actions = array())
    {
        try {
            $actions = ($actions === true) ? $permission->getAttribute('actions') : serialize($actions);
            $role->getPermissions()->save($permission, array('actions' => $actions));
        } catch (\Exception $e) {
            throw new DuplicateEntry($e->getMessage());
        }
    }

    /**
     *  Grant new role for user
     *
     * @param RoleInterface $role
     * @param UserInterface $user
     * @throws DuplicateEntry
     */
    public function grantUserRole(RoleInterface $role, UserInterface $user)
    {
        try {
            $user->getRoles()->save($role);
        } catch (\Exception $e) {
            throw new DuplicateEntry($e->getMessage());
        }
    }

    /**
     * Grant new role for group
     *
     * @param RoleInterface $role
     * @param GroupInterface $group
     * @throws DuplicateEntry
     */
    public function grantGroupRole(RoleInterface $role, GroupInterface $group)
    {
        try {
            $group->getRoles()->save($role);
        } catch (\Exception $e) {
            throw new DuplicateEntry($e->getMessage());
        }
    }


    /**
     * Revoke user permission
     *
     * @param PermissionInterface $permission
     * @param UserInterface $user
     * @return bool
     */
    public function revokeUserPermission(PermissionInterface $permission, UserInterface $user)
    {
        return $user->getPermissions()->detach($permission->getAttribute('id'));
    }

    /**
     * Revoke group permissions
     *
     * @param PermissionInterface $permission
     * @param GroupInterface $group
     * @return bool
     */
    public function revokeGroupPermission(PermissionInterface $permission, GroupInterface $group)
    {
        return $group->getPermissions()->detach($permission->getAttribute('id'));
    }

    /**
     * Revoke role permissions
     *
     * @param PermissionInterface $permission
     * @param RoleInterface $role
     * @return bool
     */
    public function revokeRolePermission(PermissionInterface $permission, RoleInterface $role)
    {
        return $role->getPermissions()->detach($permission->getAttribute('id'));
    }

    /**
     * Revoke User Role
     *
     * @param RoleInterface $role
     * @param UserInterface $user
     */
    public function revokeUserRole(RoleInterface $role, UserInterface $user)
    {
        return $user->getRoles()->detach($role->getAttribute('id'));
    }

    /**
     * Revoke Group Role
     *
     * @param RoleInterface $role
     * @param GroupInterface $group
     */
    public function revokeGroupRole(RoleInterface $role, GroupInterface $group)
    {
        return $group->getRoles()->detach($role->getAttribute('id'));
    }
}
