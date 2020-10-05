<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivitiesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $table) {
                $table->increments('id');
                $table->string('causer')->index();
                $table->string('type')->index();
                $table->string('indentifier')->index();
                $table->text('data')->nullable();
            

                // activitable
                $table->string('activitable_id');
                $table->string('activitable_type');
                $table->timestamps();
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
        Schema::dropIfExists('activities');
    }
}
