<?php

	namespace Signes\Acl;

	class Acl extends AclManager {

		/**
		 * Check if resource is available
		 *
		 * @param $resource
		 * @return bool
		 */
		public function isAllow($resource) {
			$resource_map = $this->__prepareResource($resource);
			$permissions = $this->collectPermissions();
			return $this->__compareResourceWithPermissions($resource_map, $permissions);
		}


		/**
		 * Create new permission to database, and return it's id, or false if permission exists
		 *
		 * @param $area
		 * @param $permission
		 * @param array $actions
		 * @param string $description
		 * @return mixed
		 */
		public function createPermission($area, $permission, array $actions = null, $description = '') {

			$area = (string) $area;
			$permission = (string) $permission;
			$description = (string) $description;

			return $this->repository->createPermission($area, $permission, $actions, $description);
		}

		/**
		 * Add new permission to database, and return it's id, or false if permission exists
		 *
		 * @param $area
		 * @param $permission
		 * @param array $actions
		 * @return mixed
		 */
		public function deletePermission($area, $permission = null, $actions = null) {

			$area = (string) $area;
			$permission = ($permission !== null) ? (string) $permission : null;

			return $this->repository->deletePermission($area, $permission, $actions);
		}
	}