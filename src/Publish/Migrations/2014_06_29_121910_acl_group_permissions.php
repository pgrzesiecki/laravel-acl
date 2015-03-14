<?php

use \Illuminate\Database\Migrations\Migration;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Schema;

/**
 * Class AclGroupPermissions
 */
class AclGroupPermissions extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('acl_group_permissions')) {
            Schema::create('acl_group_permissions', function ($table) {
                $table->increments('id');
                $table->timestamps();
                $table->integer('group_id');
                $table->integer('permission_id');
                $table->text('actions');
                $table->unique(['group_id', 'permission_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acl_group_permissions');
    }
}
