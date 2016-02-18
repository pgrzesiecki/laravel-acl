<?php

namespace Signes\Acl;

use Signes\Acl\Contract\AclRepository;
use Signes\Acl\Contract\GroupInterface;
use Signes\Acl\Contract\PermissionInterface;
use Signes\Acl\Contract\RoleInterface;
use Signes\Acl\Contract\UserInterface;

/**
 * Class AclManager
 *
 * @package Signes\Acl
 */
abstract class AclManager
{

    /**
     * @var AclRepository
     */
    protected $repository;
    /**
     * User in current instance.
     * Used when we want to check access many times in one request.
     * In this case we ask DB only once.
     *
     * @var UserInterface
     */
    private $currentUser;

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
     * If there is no user in local instance,
     * take user from \Auth library. If it will fail,
     * take Guest account.
     *
     * @param UserInterface $user
     */
    protected function ensureUser(UserInterface $user = null)
    {
        if ($user) {
            $this->setUser($user);
        } elseif (!$this->getUser() && !$user) {
            $this->setUser($this->repository->getGuest());
        }
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
     * @return UserInterface
     */
    protected function getUser()
    {
        return $this->currentUser;
    }

    /**
     * Collect permissions for specific user.
     *
     * @return array
     */
    protected function collectPermissions()
    {
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
        $permissionsArray = $this->collectUserPermissions($this->getUser());

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
        foreach ($this->repository->getPermissionsFor($user) as $permission) {
            $this->parsePermissions($permission, $permissionSet);
        };

        /**
         * User may have many roles permissions, iterate through all of them.
         */
        $user->getRoles->each(
            function ($role) use (&$permissionSet) {
                $this->collectRolePermission($role, $permissionSet);
            }
        );

        /**
         * User may have only one role
         */
        $this->collectGroupPermissions($user->getGroup, $permissionSet);

        return $permissionSet;
    }

    /**
     * Populate permission set.
     * Here we build part of permissions set.
     *
     * @param PermissionInterface $permission
     * @param array $permissionSet
     * @param bool $removedPopulate
     */
    protected function parsePermissions(
        PermissionInterface $permission,
        array &$permissionSet,
        $removedPopulate = false
    ) {
        $grantedActions = (array) ((isset($permission->pivot->actions)) ? unserialize(
            $permission->pivot->actions
        ) : []);
        $allowedActions = array_intersect($permission->getActions(), $grantedActions);

        if (!$removedPopulate) {
            $dotSet = "{$permission->getArea()}.{$permission->getPermission()}";
            $arrayExists = isset($permissionSet[$permission->getArea()][$permission->getPermission()]);
        } else {
            $dotSet = "_special.removed.{$permission->getArea()}.{$permission->getPermission()}";
            $arrayExists = isset($permissionSet['_special']['removed'][$permission->getArea(
                )][$permission->getPermission()]);
        }

        if (!$arrayExists) {
            array_set($permissionSet, $dotSet, $allowedActions);
        } else {
            $existed = array_get($permissionSet, $dotSet);
            array_set($permissionSet, $dotSet, array_unique(array_merge($existed, $allowedActions)));
        }
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
        $permissionSet = $this->parseSpecialRoles($role, $permissionSet);

        /**
         * Role may have many permissions, iterate through all of them.
         */
        foreach ($this->repository->getPermissionsFor($role) as $permission) {
            $this->parsePermissions($permission, $permissionSet, ($role->getFilter() === 'R'));
        };
    }

    /**
     * Check special filters added to roles.
     * We have few special roles like:
     * D - Deny, this filter deny access to ANY resource (something like banned)
     * A - Allow, this filter allow access to ANY resource (somethings like root)
     *
     * @param RoleInterface $role
     * @param array $permissionSet
     * @return array
     */
    protected function parseSpecialRoles(RoleInterface $role, array $permissionSet = [])
    {

        switch ($role->getFilter()) {
            case 'D':
                array_set($permissionSet, '_special.deny', true);
                break;
            case 'A':
                array_set($permissionSet, '_special.root', true);
                break;
        }

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
        foreach ($this->repository->getPermissionsFor($group) as $permission) {
            $this->parsePermissions($permission, $permissionSet);
        };

        /**
         * Group may have many roles, iterate through all of them.
         */
        $group->getRoles->each(
            function ($role) use (&$permissionSet) {
                $this->collectRolePermission($role, $permissionSet);
            }
        );
    }

    /**
     * Map resource to permissions array.
     *
     * @param $resource
     * @return array
     */
    protected function prepareResource($resource)
    {

        $data = explode('|', $resource);
        $areaPermission = (isset($data[0])) ? $data[0] : null;
        $actions = (isset($data[1])) ? $data[1] : null;

        // Wrong resource data? No access
        if (!$areaPermission) {
            return [
                'area'       => null,
                'permission' => null,
                'actions'    => null
            ];
        }

        // Get area and permission to check
        $areaPermission = explode('.', $areaPermission);
        $area = (isset($areaPermission[0])) ? $areaPermission[0] : null;
        $permission = (isset($areaPermission[1])) ? $areaPermission[1] : null;

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
     * @param array $resourceMap
     * @param array $permissions
     * @return bool
     */
    protected function compareResourceWithPermissions(array $resourceMap, array $permissions)
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
        if (is_array($resourceMap['actions'])) {
            foreach ($resourceMap['actions'] as $action) {
                if (!isset($permissions[$resourceMap['area']]) || !isset($permissions[$resourceMap['area']][$resourceMap['permission']]) || !in_array(
                        $action,
                        $permissions[$resourceMap['area']][$resourceMap['permission']]
                    ) || (isset($permissions['_special']['removed'][$resourceMap['area']][$resourceMap['permission']]) && in_array(
                            $action,
                            $permissions['_special']['removed'][$resourceMap['area']][$resourceMap['permission']]
                        ))
                ) {
                    $return = false;
                }
            }

            return $return;
        } else {
            return isset($permissions[$resourceMap['area']][$resourceMap['permission']]);
        }

    }
}
