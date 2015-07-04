<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class AclGroupRoles
 */
class AclGroupRoles extends Migration
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
        if (!Schema::hasTable('acl_group_roles')) {
            Schema::create('acl_group_roles', function ($table) {
                $table->integer('group_id');
                $table->integer('role_id');
                $table->primary(['group_id', 'role_id']);

                if ($this->isPGSQL()) {
                    $table->timestamp('created_at')->default(DB::raw('now()::timestamp(0)'));
                    $table->timestamp('updated_at')->default(DB::raw('now()::timestamp(0)'));
                } else {
                    $table->timestamps();
                }
            });

            DB::table('acl_group_roles')->insert(array(
                'group_id' => '1',
                'role_id'  => '1'
            ));

            DB::table('acl_group_roles')->insert(array(
                'group_id' => '2',
                'role_id'  => '2'
            ));

            DB::table('acl_group_roles')->insert(array(
                'group_id' => '3',
                'role_id'  => '3'
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
        Schema::dropIfExists('acl_group_roles');
    }
}
