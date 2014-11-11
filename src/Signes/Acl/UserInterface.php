<?php

	namespace Signes\Acl;

	interface UserInterface {

		public function getPermissions();

		public function getRoles();

		public function getGroup();

	}