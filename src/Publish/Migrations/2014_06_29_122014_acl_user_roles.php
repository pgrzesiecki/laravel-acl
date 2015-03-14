<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class AclUserRoles
 */
class AclUserRoles extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('acl_user_roles')) {
            Schema::create('acl_user_roles', function ($table) {
                $table->integer('user_id');
                $table->integer('role_id');
                $table->timestamps();
                $table->primary(['user_id', 'role_id']);
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
        Schema::dropIfExists('acl_user_roles');
    }

}
