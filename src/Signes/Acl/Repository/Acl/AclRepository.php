<?php

	namespace Signes\Acl\Repository;

	interface AclRepository {

		public function getGuest();

		public function createPermission($area, $permission, $actions = null, $description = '');

		public function deletePermission($area, $permission = null, $actions = null);
	}