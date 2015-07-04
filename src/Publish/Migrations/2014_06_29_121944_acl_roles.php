<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class AclRoles
 */
class AclRoles extends Migration
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
        if (!Schema::hasTable('acl_roles')) {
            Schema::create('acl_roles', function ($table) {
                $table->increments('id');
                $table->string('name', 255);
                $table->enum('filter', array('', 'A', 'D', 'R'));

                if ($this->isPGSQL()) {
                    $table->timestamp('created_at')->default(DB::raw('now()::timestamp(0)'));
                    $table->timestamp('updated_at')->default(DB::raw('now()::timestamp(0)'));
                } else {
                    $table->timestamps();
                }
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
    public function down()
    {
        Schema::dropIfExists('acl_roles');
    }
}
