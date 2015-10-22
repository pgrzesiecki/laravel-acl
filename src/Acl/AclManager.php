<?php

namespace Signes\Acl;

// Repository class
use Signes\Acl\Repository\AclRepository;

/**
 * Class AclManager
 *
 * @package Signes\Acl
 */
abstract class AclManager
{

    /**
     * User in current instance.
     * Used when we want to check access many times in one request.
     * In this case we ask DB only once.
     *
     * @var null|UserInterface
     */
    private $currentUser = null;

    /**
     * @var AclRepository
     */
    protected $repository;

    /**
     * Constructor
     *
     * @param AclRepository $repository
     */
    public function __construct(AclRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Set different site namespace
     *
     * @param string $namespace , new namespace
     * @return mixed
     */
    public function setSiteNamespace($namespace)
    {
        return $this->repository->setSiteNamespace($namespace);
    }

    /**
     * Set user to checks
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->currentUser = $user;
    }

    /**
     * Collect permissions for current logged in user.
     *
     * @param UserInterface $user
     * @return array
     */
    protected function collectPermissions(UserInterface $user = null)
    {
        /**
         * If there is no user in local instance,
         * take user from \Auth library. If it will fail,
         * take Guest account.
         */
        if ($user) {
            $this->setUser($user);
        } elseif (!$this->currentUser && !$user) {
            $this->setUser($this->repository->getGuest());
        }

        /**
         * Before we ask DB to collect permissions array, let's check
         * if we have required information's in cache.
         *
         * @todo Add better way to cache permissions
         */
        //$user_cache_key = 'acl:user:' . $this->currentUser->id;
        //if (\Cache::has($user_cache_key)) {
        //    return \Cache::get($user_cache_key);
        //}

        /**
         * Collect all user permissions based on their personal access, groups and roles
         */
        $permissionsArray = $this->collectUserPermissions($this->currentUser);

        /**
         * Storage permission map in cache to save time and decrease number of DB queries.
         *
         * @todo Add better way to cache permissions
         */
        //\Cache::put($user_cache_key, $permissions_array, \Config::get('signes-acl::acl.cache_time'));

        return $permissionsArray;

    }

    /**
     * Get permission set for user.
     * Included personal access, groups and roles.
     *
     * @param UserInterface $user
     * @return array
     */
    private function collectUserPermissions(UserInterface $user)
    {

        $permissionSet = [];

        /**
         * User may have many personal permissions, iterate through all of them.
         */
        $user->getPermissions->each(function ($permission) use (&$permissionSet) {
            $this->parsePermissions($permission, $permissionSet);
        });

        /**
         * User may have many roles permissions, iterate through all of them.
         */
        $user->getRoles->each(function ($role) use (&$permissionSet) {
            $this->collectRolePermission($role, $permissionSet);
        });

        /**
         * User may have only one role
         */
        $this->collectGroupPermissions($user->getGroup, $permissionSet);

        return $permissionSet;
    }

    /**
     * Collect permissions for group
     *
     * @param GroupInterface $group
     * @param array $permissionSet
     */
    private function collectGroupPermissions(GroupInterface $group, array &$permissionSet = [])
    {
        /**
         * Group may have many permissions, iterate through all of them.
         */
        $group->getPermissions->each(function ($permission) use (&$permissionSet) {
            $this->parsePermissions($permission, $permissionSet);
        });

        /**
         * Group may have many roles, iterate through all of them.
         */
        $group->getRoles->each(function ($role) use (&$permissionSet) {
            $this->collectRolePermission($role, $permissionSet);
        });
    }

    /**
     * Collect permissions for role
     *
     * @param RoleInterface $role
     * @param array $permissionSet
     */
    private function collectRolePermission(RoleInterface $role, array &$permissionSet = [])
    {

        /**
         * Roles might contain very special filters
         */
        $this->parseSpecialRoles($role, $permissionSet);

        /**
         * Role may have many permissions, iterate through all of them.
         */
        $role->getPermissions->each(function ($permission) use (&$permissionSet, $role) {
            $this->parsePermissions($permission, $permissionSet, ($role->filter === 'R'));
        });

    }

    /**
     * Check special filters added to roles.
     * We have few special roles like:
     * D - Deny, this filter deny access to ANY resource (something like banned)
     * A - Allow, this filter allow access to ANY resource (somethings like root)
     *
     * @param RoleInterface $role
     * @param array $permissionSet
     */
    private function parseSpecialRoles(RoleInterface $role, array &$permissionSet = [])
    {

        switch ($role->filter) {
            case 'D':
                array_set($permissionSet, '_special.deny', true);
                break;
            case 'A':
                array_set($permissionSet, '_special.root', true);
                break;
        }

    }

    /**
     * Populate permission set.
     * Here we build part of permissions set.
     *
     * @param PermissionInterface $permission
     * @param array $permissionSet
     * @param bool $removed_populate
     */
    private function parsePermissions(
        PermissionInterface $permission,
        array &$permissionSet,
        $removed_populate = false
    ) {
        $permission_actions = (array) ((isset($permission->actions)) ?
            unserialize($permission->actions) : []);
        $granted_actions = (array) ((isset($permission->pivot->actions)) ?
            unserialize($permission->pivot->actions) : []);
        $allowed_actions = array_intersect($permission_actions, $granted_actions);

        if (!$removed_populate) {
            $dot_set = $permission->area . '.' . $permission->permission;
            $array_exists = isset($permissionSet[$permission->area][$permission->permission]);
        } else {
            $dot_set = '_special.removed.' . $permission->area . '.' . $permission->permission;
            $array_exists = isset($permissionSet['_special']['removed'][$permission->area][$permission->permission]);
        }

        if (!$array_exists) {
            array_set($permissionSet, $dot_set, $allowed_actions);
        } else {
            $existed = array_get($permissionSet, $dot_set);
            array_set($permissionSet, $dot_set, array_unique(array_merge($existed, $allowed_actions)));
        }
    }

    /**
     * Map resource to permissions array.
     *
     * @param $resource
     * @return array
     */
    protected function __prepareResource($resource)
    {

        $data = explode('|', $resource);
        $area_permission = (isset($data[0])) ? $data[0] : null;
        $actions = (isset($data[1])) ? $data[1] : null;

        // Wrong resource data? No access
        if (!$area_permission) {
            return [
                'area'       => null,
                'permission' => null,
                'actions'    => null
            ];
        }

        // Get area and permission to check
        $area_permission = explode('.', $area_permission);
        $area = (isset($area_permission[0])) ? $area_permission[0] : null;
        $permission = (isset($area_permission[1])) ? $area_permission[1] : null;

        // Get actions to check
        $actions = explode('.', $actions);
        if (!is_array($actions) || empty($actions) || $actions[0] === '') {
            $actions = null;
        }

        return [
            'area'       => $area,
            'permission' => $permission,
            'actions'    => $actions
        ];
    }

    /**
     * Compare Resource with Permission array.
     * Check request to resource with user access set.
     *
     * @param array $resource_map
     * @param array $permissions
     * @return bool
     */
    protected function __compareResourceWithPermissions(array $resource_map, array $permissions)
    {

        /**
         * Check if user have root access (user is member of role with special filter)
         */
        if (isset($permissions['_special.root']) && $permissions['_special.root'] === true) {
            return true;
        }

        /**
         * Check if user is blocked (user is member of role with special filter)
         */
        if (isset($permissions['_special.deny']) && $permissions['_special.deny'] === true) {
            return false;
        }

        /**
         * If we do not have special roles, check build access array.
         * By default user have access to resource.
         */
        $return = true;

        /**
         * If we request "actions" in resource, go through all requested actions and check
         * if we have access to all of this actions in requested area and permission name,
         * or if action was not blocked.
         *
         * @see https://github.com/signes-pl/laravel-acl
         */
        if (is_array($resource_map['actions'])) {
            foreach ($resource_map['actions'] as $action) {
                if (
                    !isset($permissions[$resource_map['area']]) ||
                    !isset($permissions[$resource_map['area']][$resource_map['permission']]) ||
                    !in_array($action, $permissions[$resource_map['area']][$resource_map['permission']]) ||
                    (
                        isset($permissions['_special']['removed'][$resource_map['area']][$resource_map['permission']]) &&
                        in_array(
                            $action,
                            $permissions['_special']['removed'][$resource_map['area']][$resource_map['permission']]
                        )
                    )
                ) {
                    $return = false;
                }
            }

            return $return;
        } else {
            return isset($permissions[$resource_map['area']][$resource_map['permission']]);
        }

    }
}
