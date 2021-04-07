<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeSettingsValueNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('users', 'activated')) {
            Schema::table(
                'users', function (Blueprint $table) {
                    $table->boolean('activated')->default(true);
                }
            );
        }
        if (!Schema::hasColumn('roles', 'label')) {
            Schema::table(
                'roles', function (Blueprint $table) {
                    $table->string('label')->nullable();
                }
            );
        }


        if (!Schema::hasColumn('roles', 'permissions')) {
            Schema::table(
                'roles', function (Blueprint $table) {
                    $table->string('permissions')->nullable();
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
        
    }
}
