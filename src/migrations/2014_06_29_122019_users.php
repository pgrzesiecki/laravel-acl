<?php

	use \Illuminate\Database\Migrations\Migration;
	use \Illuminate\Support\Facades\Schema;
	use Illuminate\Support\Facades\DB;

	class Users extends Migration {

		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up() {
			if(!Schema::hasTable('users')) {
				Schema::create('users', function ($table) {
					$table->increments('id');
					$table->timestamps();
					$table->string('login', 255);
					$table->string('password', 255);
					$table->string('group_id', 255);
					$table->string('remember_token', 150);
				});

				DB::table('users')->insert(array(
					'id'       => '1',
					'login'    => 'guest',
					'password' => 'NO PASSWORD',
					'group_id' => '2'
				));
			}
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down() {
			Schema::dropIfExists('users');
		}

	}
