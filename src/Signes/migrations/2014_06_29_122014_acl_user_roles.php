<?php

	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;

	class AclUserRoles extends Migration {

		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			if(!Schema::hasTable('acl_user_roles')) {
				Schema::create('acl_user_roles', function ($table) {
					$table->integer('user_id');
					$table->integer('role_id');
					$table->timestamps();
					$table->primary(array('user_id', 'role_id'));
				});
			}
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('acl_user_roles');
		}

	}
