<?php

	namespace Signes\Acl;

	interface PermissionInterface {

		public function getAreaPermission($area, $permission);

	}