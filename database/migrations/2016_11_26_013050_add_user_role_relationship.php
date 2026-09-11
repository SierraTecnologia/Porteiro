<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserRoleRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('users')) {
            Schema::table(
                'users', function (Blueprint $table) {
                    if (Schema::hasColumn('users', 'role_id')) {
                        $table->integer('role_id')->unsigned()->nullable()->change();
                        if (Schema::hasTable('roles')) {
                            $table->foreign('role_id')->references('id')->on('roles');
                        }
                    }

                    if (! Schema::hasColumn('users', 'admin')) {
                        $table->integer('admin')->unsigned()->default(0);
                    }
                    if (! Schema::hasColumn('users', 'deleted_at')) {
                        $table->softDeletes();
                    }
                }
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('users')) {
            Schema::table(
                'users', function (Blueprint $table) {
                    if (Schema::hasColumn('users', 'role_id')) {
                        $table->dropForeign(['role_id']);
                    }
                }
            );
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role_id')) {
            Schema::table(
                'users', function (Blueprint $table) {
                    $table->bigInteger('role_id')->nullable()->change();
                }
            );
        }
    }
}
