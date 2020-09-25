<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'settings',
            function (Blueprint $table) {
                // @todo resolver lance do bussiness_code
                $table->engine = 'InnoDB';
                // $table->increments('id');
                // $table->string('code')->unique();
                // $table->primary('code');
                $table->string('setting_key')->primary()->indexed();
                $table->text('value');
                
                $table->timestamps();
                $table->softDeletes();
                
                // $table->primary(['code', 'business_code']);

                // $table->increments('id');
                // $table->string('key')->unique();
                $table->string('display_name')->nullable()->default(null);
                $table->text('details')->nullable()->default(null);
                $table->string('type')->nullable()->default(null);
                $table->integer('order')->default('1');
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
        Schema::dropIfExists('settings');
    }
}
