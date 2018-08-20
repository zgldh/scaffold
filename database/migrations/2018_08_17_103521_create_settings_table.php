<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('z_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('内部编号');
            $table->json('value')->comment('取值');
            $table->integer('settable_id')->nullable()->comment('可配置对象ID');
            $table->string('settable_type')->nullable()->comment('可配置对象类型');
            $table->timestamps();

            $table->index(['name', 'settable_id', 'settable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('z_settings');
    }
}
