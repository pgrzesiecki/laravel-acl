<?php

	namespace Acl;

	use Illuminate\Auth\UserTrait;
	use Illuminate\Auth\UserInterface;
	use Illuminate\Auth\Reminders\RemindableTrait;
	use Illuminate\Auth\Reminders\RemindableInterface;

	class User extends \Eloquent implements UserInterface, RemindableInterface {

		use UserTrait, RemindableTrait;

		/**
		 * The database table used by the model.
		 *
		 * @var string
		 */
		protected $table = 'users';

		/**
		 * The attributes excluded from the model's JSON form.
		 *
		 * @var array
		 */
		protected $hidden = array('password', 'remember_token');

		/**
		 * User personal permissions
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
		 */
		public function getPermissions() {
			return $this->belongsToMany('Acl\\Permission', 'acl_user_permissions', 'user_id', 'permission_id')->withPivot('actions');
		}

		/**
		 * Get user roles
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
		 */
		public function getRoles() {
			return $this->belongsToMany('Acl\\Role', 'acl_user_roles', 'user_id', 'role_id');
		}

		/**
		 * Get user group
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\HasOne
		 */
		public function getGroup() {
			return $this->hasOne('Acl\\Group', 'id', 'group_id');
		}
	}
