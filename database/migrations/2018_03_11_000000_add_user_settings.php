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
        if (Schema::hasTable('users')) {
            Schema::table(
                'users', function (Blueprint $table) {
                    if (! Schema::hasColumn('users', 'settings')) {
                        $table->text('settings')->nullable()->default(null)->after('remember_token');
                    }

                    // @todo verificar isso, add pra parar de dar erro

                    if (! Schema::hasColumn('users', 'locale')) {
                        $table->string('locale')->nullable()->default('pt');
                    }
                    if (! Schema::hasColumn('users', 'locale_group')) {
                        $table->string('locale_group')->nullable();
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
                    foreach (['locale_group', 'locale', 'settings'] as $column) {
                        if (Schema::hasColumn('users', $column)) {
                            $table->dropColumn($column);
                        }
                    }
                }
            );
        }
    }
}
