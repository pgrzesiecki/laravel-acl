<?php

	namespace Signes\Acl;

	interface GroupInterface {

		public function getPermissions();

		public function getRoles();

	}