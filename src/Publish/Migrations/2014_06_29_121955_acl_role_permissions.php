<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class AclRolePermissions
 */
class AclRolePermissions extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('acl_role_permissions')) {
            Schema::create('acl_role_permissions', function ($table) {
                $table->increments('id');
                $table->timestamps();
                $table->integer('role_id');
                $table->integer('permission_id');
                $table->text('actions');
                $table->unique(['role_id', 'permission_id']);
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
        Schema::dropIfExists('acl_role_permissions');
    }
}
