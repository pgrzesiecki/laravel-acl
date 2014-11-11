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

	}