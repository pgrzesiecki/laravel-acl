<?php

	namespace Acl;

	class Group extends \Eloquent {

		/**
		 * The database table used by the model.
		 *
		 * @var string
		 */
		protected $table = 'acl_groups';

		/**
		 * User group permissions
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
		 */
		public function getPermissions() {
			return $this->belongsToMany('Acl\\Permission', 'acl_group_permissions', 'group_id', 'permission_id')->withPivot('actions');
		}

		/**
		 * Get group roles
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
		 */
		public function getRoles() {
			return $this->belongsToMany('Acl\\Role', 'acl_group_roles', 'group_id', 'role_id');
		}
	}
