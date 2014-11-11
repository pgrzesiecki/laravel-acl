<?php

	namespace Signes\Acl;

	use Acl\Role;
	use Acl\Permission;
	use Acl\Group;
	use Illuminate\Support\Facades\Auth;

	abstract class AclManager {

		private $_current_user = null;

		private $_cache_time = 5;

		/**
		 * Add new permission to database, and return it's id, or false if permission exists
		 *
		 * @param $area
		 * @param $permission
		 * @param array $actions
		 * @param string $description
		 * @return Permission|bool
		 */
		public function addPermission($area, $permission, array $actions = null, $description = '') {

			$area = (string) $area;
			$permission = (string) $permission;
			$description = (string) $description;

			$permission_exists = Permission::where('area', '=', $area)->where('permission', '=', $permission)->first();

			if(!$permission_exists) {

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
		 * Collect permissions for current logged in user
		 *
		 * @return array
		 */
		public function collectPermissions() {

			if(!$this->_current_user) {
				$user = Auth::user();
				if(!$user) {
					$user = \User::find(0);
				}
				$this->_current_user = $user;
			}

			// Cache
			$user_cache_key = 'acl:user:' . $this->_current_user->id;
			if(\Cache::has($user_cache_key)) {
				return \Cache::get($user_cache_key);
			}

			// Run user collection
			$permissions_array = $this->collectUserPermissions($this->_current_user);

			// Put to cache
			\Cache::put($user_cache_key, $permissions_array, $this->_cache_time);

			return $permissions_array;

		}

		/**
		 * Get permission set for user
		 *
		 * @param \User $user
		 * @return array
		 */
		public function collectUserPermissions(\User $user) {

			$permission_set = array();
			$user->getPermissions->each(function ($permission) use (&$permission_set) {
				$this->_parsePermissions($permission, $permission_set);
			});

			$user->getRoles->each(function ($role) use (&$permission_set) {
				$this->collectRolePermission($role, $permission_set);
			});

			$this->collectGroupPermissions($user->getGroup, $permission_set);

			return $permission_set;

		}

		/**
		 * Collect permissions for group
		 *
		 * @param Group $group
		 * @param array $permission_set
		 */
		public function collectGroupPermissions(Group $group, array &$permission_set = array()) {

			$group->getPermissions->each(function ($permission) use (&$permission_set) {
				$this->_parsePermissions($permission, $permission_set);
			});

			$group->getRoles->each(function ($role) use (&$permission_set) {
				$this->collectRolePermission($role, $permission_set);
			});
		}

		/**
		 * Collect permissions for role
		 *
		 * @param Role $role
		 * @param array $permission_set
		 */
		public function collectRolePermission(Role $role, array &$permission_set = array()) {

			$this->_parseSpecialRoles($role, $permission_set);

			$role->getPermissions->each(function ($permission) use (&$permission_set, $role) {
				$this->_parsePermissions($permission, $permission_set, ($role->filter === 'R'));
			});

		}

		private function _parseSpecialRoles(Role $role, array &$permission_set = array()) {

			switch($role->filter) {
				case 'D':
					array_set($permission_set, '_special.deny', true);
					break;
				case 'A':
					array_set($permission_set, '_special.root', true);
					break;
			}

		}

		/**
		 * Populate permission set
		 *
		 * @param Permission $permission
		 * @param array $permission_set
		 * @param bool $removed_populate
		 */
		private function _parsePermissions(Permission $permission, array & $permission_set, $removed_populate = false) {
			$permission_actions = (isset($permission->actions)) ? unserialize($permission->actions) : array();
			$granted_actions = (isset($permission->pivot->actions)) ? unserialize($permission->pivot->actions) : array();
			$allowed_actions = array_intersect($permission_actions, $granted_actions);

			if(!$removed_populate) {
				$dot_set = $permission->area . '.' . $permission->permission;
				$array_exists = isset($permission_set[$permission->area][$permission->permission]);
			} else {
				$dot_set = '_special.removed.' . $permission->area . '.' . $permission->permission;
				$array_exists = isset($permission_set['_special']['removed'][$permission->area][$permission->permission]);
			}

			if(!$array_exists) {
				array_set($permission_set, $dot_set, $allowed_actions);
				//$permission_set[$permission->area][$permission->permission] = $allowed_actions;
			} else {
				$existed = array_get($permission_set, $dot_set);
				array_set($permission_set, $dot_set, array_unique(array_merge($existed, $allowed_actions)));
			}
		}

		/**
		 * Map resource to permissions array
		 *
		 * @param $resource
		 * @return bool
		 */
		protected function __prepareResource($resource) {

			$data = explode('|', $resource);
			$area_permission = (isset($data[0])) ? $data[0] : null;
			$actions = (isset($data[1])) ? $data[1] : null;

			// Wrong resource data? No access
			if(!$area_permission || count($data) !== 2) {
				return false;
			}

			// Get area and permission to check
			list($area, $permission) = explode('.', $area_permission);

			// Get actions to check
			$actions = explode('.', $actions);
			if(!is_array($actions) || empty($actions) || $actions[0] === '') {
				$actions = null;
			}

			return array(
				'area'       => $area,
				'permission' => $permission,
				'actions'    => $actions
			);
		}

		/**
		 * Compare Resource with Permission array
		 *
		 * @param array $resource_map
		 * @param array $permissions
		 * @return bool
		 */
		protected function __compareResourceWithPermissions(array $resource_map, array $permissions) {

			// special permissions
			if(isset($permissions['_special.root']) && $permissions['_special.root'] === true) {
				return true;
			}

			if(isset($permissions['_special.deny']) && $permissions['_special.deny'] === true) {
				return false;
			}

			// Regular permissions
			$return = true;
			if(is_array($resource_map['actions'])) {

				foreach($resource_map['actions'] as $action) {
					if(
						!isset($permissions[$resource_map['area']]) ||
						!isset($permissions[$resource_map['area']][$resource_map['permission']]) ||
						!in_array($action, $permissions[$resource_map['area']][$resource_map['permission']]) ||
						(
							isset($permissions['_special']['removed'][$resource_map['area']][$resource_map['permission']]) &&
							in_array($action, $permissions['_special']['removed'][$resource_map['area']][$resource_map['permission']])
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