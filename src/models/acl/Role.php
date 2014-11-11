<?php

	namespace Acl;

	class Role extends \Eloquent {

		/**
		 * The database table used by the model.
		 *
		 * @var string
		 */
		protected $table = 'acl_roles';

		/**
		 * User role permissions
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
		 */
		public function getPermissions() {
			return $this->belongsToMany('Acl\\Permission', 'acl_role_permissions', 'role_id', 'permission_id')->withPivot('actions');
		}

	}
