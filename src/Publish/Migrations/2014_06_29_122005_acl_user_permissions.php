<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class AclUserPermissions
 */
class AclUserPermissions extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('acl_user_permissions')) {
            Schema::create('acl_user_permissions', function ($table) {
                $table->increments('id');
                $table->timestamps();
                $table->integer('user_id');
                $table->integer('permission_id');
                $table->text('actions');
                $table->unique(['user_id', 'permission_id']);
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
        Schema::dropIfExists('acl_user_permissions');
    }
}
