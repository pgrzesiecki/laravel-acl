<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class AclPermissions
 */
class AclPermissions extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('acl_permissions')) {
            Schema::create('acl_permissions', function ($table) {
                $table->increments('id');
                $table->timestamps();
                $table->string('area', 25);
                $table->string('permission', 25);
                $table->text('actions');
                $table->string('description', 255);
                $table->unique(['area', 'permission']);
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
        Schema::dropIfExists('acl_permissions');
    }
}
