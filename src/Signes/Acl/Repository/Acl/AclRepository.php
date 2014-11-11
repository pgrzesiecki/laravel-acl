<?php

	namespace Signes\Acl\Repository;

	interface AclRepository {

		public function getAuth();

		public function getGuest();

		public function cacheHas($cacheKey);

		public function cacheGet($cacheKey);

		public function cachePut($cacheKey, $cacheValue);

		public function createPermission($area, $permission, $actions = null, $description = '');

		public function deletePermission($area, $permission = null, $actions = null);
	}