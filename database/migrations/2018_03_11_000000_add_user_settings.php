<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'users', function (Blueprint $table) {
                $table->text('settings')->nullable()->default(null)->after('remember_token');

                // @todo verificar isso, add pra parar de dar erro

                $table->string('locale')->nullable()->default('pt');
                $table->string('locale_group')->nullable();
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
        Schema::table(
            'users', function (Blueprint $table) {
                $table->dropColumn('locale_group');
                $table->dropColumn('locale');
                $table->dropColumn('settings');
            }
        );
    }
}
