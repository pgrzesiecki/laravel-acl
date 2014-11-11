<?php

	namespace Signes\Acl\Repository;

	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Cache;
	use Illuminate\Support\Facades\Config;
	use Signes\Acl\Model\Permission;
	use Signes\Acl\Model\User;

	class SignesAclRepository implements AclRepository {

		/**
		 * Get user object from Laravel Auth mechanism
		 *
		 * @return mixed
		 */
		public function getAuth() {
			return Auth::user();
		}

		/**
		 * Get Guest object
		 *
		 * @return mixed
		 */
		public function getGuest() {
			return User::find(1);
		}

		/**
		 * @param $cacheKey
		 * @return mixed
		 */
		public function cacheHas($cacheKey) {
			return Cache::has($cacheKey);
		}

		/**
		 * Get cache value based on key
		 *
		 * @param $cacheKey
		 * @return mixed
		 */
		public function cacheGet($cacheKey) {
			return Cache::get($cacheKey);
		}

		/**
		 * Put cache data.
		 *
		 * @param $cacheKey
		 * @param $cacheValue
		 */
		public function cachePut($cacheKey, $cacheValue) {
			\Cache::put($cacheKey, $cacheValue, Config::get('signes-acl::acl.cache_time'));
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
		public function createPermission($area, $permission, $actions = null, $description = '') {

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
		 * Delete permission from base.
		 * We can remove whole zone, zone with permission, or one specific action.
		 *
		 * @param $area
		 * @param null $permission
		 * @param null $actions
		 * @return bool
		 */
		public function deletePermission($area, $permission = null, $actions = null) {

			if($permission === null && $actions === null) {
				return Permission::where('area', '=', $area)->delete();
			}

			if(is_string($permission) && $actions === null) {
				return Permission::where('area', '=', $area)->where('permission', '=', $permission)->delete();
			}

			if(is_string($permission) && $actions !== null) {
				$actions = !is_array($actions) ? array($actions) : $actions;
				$permission = Permission::where('area', '=', $area)->where('permission', '=', $permission)->first();

				if($permission) {
					$currentActions = unserialize($permission->actions);
					if(is_array($currentActions)) {
						foreach($actions as $action) {
							if(($key = array_search($action, $currentActions)) !== false) {
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
	}