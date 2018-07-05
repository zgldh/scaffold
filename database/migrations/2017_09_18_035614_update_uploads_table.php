<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUploadsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('z_uploads', function (Blueprint $table) {
            $table->string('type')->default('');

            $table->dropIndex('uploadable_index');
            $table->index([
                'uploadable_id',
                'uploadable_type',
                'type'
            ], 'uploadable_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('z_uploads', function (Blueprint $table) {
            $table->dropIndex('uploadable_index');
            $table->dropColumn('type');
            $table->index([
                'uploadable_id',
                'uploadable_type'
            ], 'uploadable_index');
        });
    }

}
