<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('permission_role')) {
            // Schema::create(
            //     'permission_role', function (Blueprint $table) {
            //         $table->integer('permission_id')->unsigned()->index();
            //         $table->integer('role_id')->unsigned()->index();

            //         $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            //         $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            //         $table->primary(['permission_id', 'role_id']);
            //     }
            // );

            // From Entrust
            // Create table for associating permissions to roles (Many-to-Many)
            Schema::create(
                'permission_role', function (Blueprint $table) {
                    $table->integer('permission_id')->unsigned();
                    $table->integer('role_id')->unsigned();

                    $table->foreign('permission_id')->references('id')->on('permissions')
                        ->onUpdate('cascade')->onDelete('cascade');
                    $table->foreign('role_id')->references('id')->on('roles')
                        ->onUpdate('cascade')->onDelete('cascade');

                    $table->primary(['permission_id', 'role_id']);
                    $table->timestamps();
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
        Schema::dropIfExists('permission_role');
    }
}
