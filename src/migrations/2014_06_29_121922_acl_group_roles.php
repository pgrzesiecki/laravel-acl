<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AclGroupRoles extends Migration
{

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
                $table->timestamps();
                $table->primary(array('group_id', 'role_id'));
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
