<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VerifyUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        /**
         * user
         */
        Schema::table(
            'users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'active')) {
                    $table->boolean('active')->default(true);
                }
                if (!Schema::hasColumn('users', 'gender')) {
                    $table->string('gender', 12)->nullable();
                }
                if (!Schema::hasColumn('users', 'birth_date')) {
                    $table->date('birth_date')->nullable();
                }
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('active');
            $table->dropColumn('gender');
            $table->dropColumn('birth_date');
        });
    }
}
