<?php

namespace Signes\Acl\Repository;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Signes\Acl\Exception\DuplicateEntry;
use Signes\Acl\Model\Permission;
use Signes\Acl\Model\User;
use Signes\Acl\PermissionInterface;
use Signes\Acl\UserInterface;

class SignesAclRepository implements AclRepository
{

    /**
     * Get Guest object
     *
     * @return mixed
     */
    public function getGuest()
    {
        return User::find(1);
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

        $permission_exists = Permission::where('area', '=', $area)->where('permission', '=', $permission)->first();

        if (!$permission_exists) {
            $new_permission = new Permission();
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

        if ($permission === null && $actions === null) {
            return Permission::where('area', '=', $area)->delete();
        }

        if (is_string($permission) && $actions === null) {
            return Permission::where('area', '=', $area)->where('permission', '=', $permission)->delete();
        }

        if (is_string($permission) && $actions !== null) {
            $actions = !is_array($actions) ? array($actions) : $actions;
            $permission = Permission::where('area', '=', $area)->where('permission', '=', $permission)->first();

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
     * @param PermissionInterface $permission
     * @param UserInterface $user
     * @param array $actions , actions array or true, if true all actions will be revoked
     */
    public function revokeUserPermission(PermissionInterface $permission, UserInterface $user, $actions = array())
    {

        $user->getPermissions()->detach($permission->getAttribute('id'));

        //			try {
        //				$actions = ($actions === true) ? serialize($permission->getAttribute('actions')) : serialize($actions);
        //				if($actions === true) {
        //					$user->getPermissions()
        //				} else {
        //					$user->getPermissions()->updateExistingPivot($permission->getAttribute('id'), array('actions' => serialize(array('q'))));
        //				}
        //			} catch(\Exception $e){
        //				var_dump($e->getMessage(), $e->getPrevious());
        //			}

    }
}
