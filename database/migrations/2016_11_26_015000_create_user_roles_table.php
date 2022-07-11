<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('role_user')) {
            // Create table for associating roles to users (Many-to-Many)
            Schema::create('role_user', function (Blueprint $table) {
                $table->integer('user_id')->unsigned();
                $table->integer('role_id')->unsigned();

                $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

                $table->primary(['user_id', 'role_id']);
                $table->timestamps();
            });
        }
        // Schema::create(
        //     'role_user', function (Blueprint $table) {
        //         $type = DB::connection()->getDoctrineColumn(DB::getTablePrefix().'users', 'id')->getType()->getName();
        //         if ($type == 'bigint') {
        //             $table->bigInteger('user_id')->unsigned()->index();
        //         } else {
        //             $table->integer('user_id')->unsigned()->index();
        //         }

        //         $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //         $table->bigInteger('role_id')->unsigned()->index();
        //         $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        //         $table->primary(['user_id', 'role_id']);
        //     }
        // );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_user');
    }
}
