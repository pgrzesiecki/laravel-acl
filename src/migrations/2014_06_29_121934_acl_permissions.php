<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
