<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AclUsers extends Migration
{

    /**
     * Detect PostgreSQL database
     *
     * @return bool
     */
    public function isPGSQL()
    {
        $driver = \Config::get('database.default');
        return \Config::get("database.connections.{$driver}.driver") === 'pgsql';
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('acl_users')) {
            Schema::create('acl_users', function ($table) {
                $table->increments('id');
                $table->string('login', 255);
                $table->string('password', 255);
                $table->string('group_id', 255);
                $table->string('remember_token', 150)->nullable();

                if ($this->isPGSQL()) {
                    $table->timestamp('created_at')->default(DB::raw('now()::timestamp(0)'));
                    $table->timestamp('updated_at')->default(DB::raw('now()::timestamp(0)'));
                } else {
                    $table->timestamps();
                }
            });

            /**
             * Add first user - guest
             */
            DB::table('acl_users')->insert(array(
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
    public function down()
    {
        Schema::dropIfExists('acl_users');
    }

}
