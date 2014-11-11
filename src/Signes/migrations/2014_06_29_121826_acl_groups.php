<?php

	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;

	class AclGroups extends Migration {

		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {

			if(!Schema::hasTable('acl_groups')) {
				Schema::create('acl_groups', function ($table) {
					$table->increments('id');
					$table->timestamps();
					$table->string('name', 255);
				});

				DB::table('acl_groups')->insert(array(
					'id'   => '1',
					'name' => 'Banned'
				));

				DB::table('acl_groups')->insert(array(
					'id'   => '2',
					'name' => 'Guests'
				));

				DB::table('acl_groups')->insert(array(
					'id'   => '3',
					'name' => 'Users'
				));
			}


		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('acl_groups');
		}

	}
