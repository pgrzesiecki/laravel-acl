<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class AclGroups
 */
class AclGroups extends Migration
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

        if (!Schema::hasTable('acl_groups')) {
            Schema::create('acl_groups', function ($table) {
                $table->increments('id');
                $table->string('name', 255);

                if ($this->isPGSQL()) {
                    $table->timestamp('created_at')->default(DB::raw('now()::timestamp(0)'));
                    $table->timestamp('updated_at')->default(DB::raw('now()::timestamp(0)'));
                } else {
                    $table->timestamps();
                }
            });

            DB::table('acl_groups')->insert(array(
                'id'   => '1',
                'name' => 'Banned'
            ));

            DB::table('acl_groups')->insert(array(
                'id'   => '2',
                'name' => 'Guests'
            ));

            DB::table('acl_groups')->insert(array(
                'id'   => '3',
                'name' => 'Users'
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
        Schema::dropIfExists('acl_groups');
    }
}
