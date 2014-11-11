<?php

	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;

	class AclRoles extends Migration {

		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			if(!Schema::hasTable('acl_roles')) {
				Schema::create('acl_roles', function ($table) {
					$table->increments('id');
					$table->timestamps();
					$table->string('name', 255);
					$table->enum('filter', array('', 'A', 'D', 'R'));
				});

				DB::table('acl_roles')->insert(array(
					'id'     => 1,
					'name'   => 'banned',
					'filter' => 'D'
				));

				DB::table('acl_roles')->insert(array(
					'id'     => 2,
					'name'   => 'public',
					'filter' => ''
				));

				DB::table('acl_roles')->insert(array(
					'id'     => 3,
					'name'   => 'user',
					'filter' => ''
				));
			}
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('acl_roles');
		}

	}
