<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('permissions')) {
            Schema::create(
                'permissions',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('name')->nullable(); // @todo ->unique();
                    $table->string('display_name')->nullable();
                    $table->string('description')->nullable();
                
                    // Sem ser do entrust
                    $table->string('key')->nullable()->index();
                    $table->string('table_name')->nullable();
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
        Schema::dropIfExists('permissions');
    }
}
