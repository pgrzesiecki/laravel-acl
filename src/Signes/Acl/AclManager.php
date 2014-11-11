<?php

	namespace Signes\Acl;

	// Repository class
	use Signes\Acl\Repository\AclRepository;

	abstract class AclManager {

		/**
		 * User in current instance.
		 * Used when we want to check access many times in one request.
		 * In this case we ask DB only once.
		 *
		 * @var null
		 */
		private $_current_user = null;

		/**
		 * @param AclRepository $repository
		 */
		public function __construct(AclRepository $repository) {
			$this->repository = $repository;
		}

		/**
		 * Collect permissions for current logged in user.
		 *
		 * @return array
		 */
		public function collectPermissions() {

			/**
			 * If there is no user in local instance,
			 * take user from \Auth library. If it will fail,
			 * take Guest account.
			 */
			if(!$this->_current_user) {
				$user = $this->repository->getAuth();
				if(!$user) {
					$user = $this->repository->getGuest();
				}

				// Set user to current instance
				$this->_current_user = $user;
			}

			/**
			 * Before we ask DB to collect permissions array, let's check
			 * if we have required information's in cache.
			 */
			$user_cache_key = 'acl:user:' . $this->_current_user->id;
			if($this->repository->cacheHas($user_cache_key)) {
				return $this->repository->cacheGet($user_cache_key);
			}

			/**
			 * Collect all user permissions based on their personal access, groups and roles
			 */
			$permissions_array = $this->collectUserPermissions($this->_current_user);

			/**
			 * Storage permission map in cache to save time and decrease number of DB queries.
			 */
			$this->repository->cachePut($user_cache_key, $permissions_array);

			return $permissions_array;

		}

		/**
		 * Get permission set for user.
		 * Included personal access, groups and roles.
		 *
		 * @param UserInterface $user
		 * @return array
		 */
		public function collectUserPermissions(UserInterface $user) {

			$permission_set = array();

			/**
			 * User may have many personal permissions, iterate through all of them.
			 */
			$user->getPermissions->each(function ($permission) use (&$permission_set) {
				$this->_parsePermissions($permission, $permission_set);
			});

			/**
			 * User may have many roles permissions, iterate through all of them.
			 */
			$user->getRoles->each(function ($role) use (&$permission_set) {
				$this->collectRolePermission($role, $permission_set);
			});

			/**
			 * User may have only one role
			 */
			$this->collectGroupPermissions($user->getGroup, $permission_set);

			return $permission_set;

		}

		/**
		 * Collect permissions for group
		 *
		 * @param GroupInterface $group
		 * @param array $permission_set
		 */
		public function collectGroupPermissions(GroupInterface $group, array &$permission_set = array()) {

			/**
			 * Group may have many permissions, iterate through all of them.
			 */
			$group->getPermissions->each(function ($permission) use (&$permission_set) {
				$this->_parsePermissions($permission, $permission_set);
			});

			/**
			 * Group may have many roles, iterate through all of them.
			 */
			$group->getRoles->each(function ($role) use (&$permission_set) {
				$this->collectRolePermission($role, $permission_set);
			});
		}

		/**
		 * Collect permissions for role
		 *
		 * @param RoleInterface $role
		 * @param array $permission_set
		 */
		public function collectRolePermission(RoleInterface $role, array &$permission_set = array()) {

			/**
			 * Roles might contain very special filters
			 */
			$this->_parseSpecialRoles($role, $permission_set);

			/**
			 * Role may have many permissions, iterate through all of them.
			 */
			$role->getPermissions->each(function ($permission) use (&$permission_set, $role) {
				$this->_parsePermissions($permission, $permission_set, ($role->filter === 'R'));
			});

		}

		/**
		 * Check special filters added to roles.
		 * We have few special roles like:
		 * D - Deny, this filter deny access to ANY resource (something like banned)
		 * A - Allow, this filter allow access to ANY resource (somethings like root)
		 *
		 * @param RoleInterface $role
		 * @param array $permission_set
		 */
		private function _parseSpecialRoles(RoleInterface $role, array &$permission_set = array()) {

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
		 * Populate permission set.
		 * Here we build part of permissions set.
		 *
		 * @param PermissionInterface $permission
		 * @param array $permission_set
		 * @param bool $removed_populate
		 */
		private function _parsePermissions(PermissionInterface $permission, array &$permission_set, $removed_populate = false) {

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
			} else {
				$existed = array_get($permission_set, $dot_set);
				array_set($permission_set, $dot_set, array_unique(array_merge($existed, $allowed_actions)));
			}
		}

		/**
		 * Map resource to permissions array.
		 *
		 * @param $resource
		 * @return array
		 */
		protected function __prepareResource($resource) {

			$data = explode('|', $resource);
			$area_permission = (isset($data[0])) ? $data[0] : null;
			$actions = (isset($data[1])) ? $data[1] : null;

			// Wrong resource data? No access
			if(!$area_permission) {
				return array(
					'area'       => null,
					'permission' => null,
					'actions'    => null
				);
			}

			// Get area and permission to check
			$area_permission = explode('.', $area_permission);
			$area = (isset($area_permission[0])) ? $area_permission[0] : null;
			$permission = (isset($area_permission[1])) ? $area_permission[1] : null;

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
		 * Compare Resource with Permission array.
		 * Check request to resource with user access set.
		 *
		 * @param array $resource_map
		 * @param array $permissions
		 * @return bool
		 */
		protected function __compareResourceWithPermissions(array $resource_map, array $permissions) {

			/**
			 * Check if user have root access (user is member of role with special filter)
			 */
			if(isset($permissions['_special.root']) && $permissions['_special.root'] === true) {
				return true;
			}

			/**
			 * Check if user is blocked (user is member of role with special filter)
			 */
			if(isset($permissions['_special.deny']) && $permissions['_special.deny'] === true) {
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