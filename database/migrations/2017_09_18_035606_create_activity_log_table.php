<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('z_activity_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('log_name')->nullable();
            $table->string('description');
            $table->integer('subject_id')->nullable();
            $table->string('subject_type')->nullable();
            $table->integer('causer_id')->nullable();
            $table->string('causer_type')->nullable();
            $table->text('properties')->nullable();
            $table->string('collector_type')->nullable();
            $table->integer('collector_id')->nullable();

            $table->timestamps();

            $table->index('log_name');			
            $table->index(['collector_type', 'collector_id'], 'collector_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('z_activity_log');
    }
}
