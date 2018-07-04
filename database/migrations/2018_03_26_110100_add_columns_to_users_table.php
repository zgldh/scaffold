<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('z_users', function (Blueprint $table) {
            $table->string('gender', 8)->nullable(); // Male, Female
            $table->string('mobile')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('last_login_at')->nullable();
            $table->integer('login_times')->default(0);
            $table->integer('avatar_id')->nullable();
            $table->softDeletes();
        });
        Schema::table('z_roles', function (Blueprint $table) {
            $table->string('label');
        });
        Schema::table('z_permissions', function (Blueprint $table) {
            $table->string('label');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('z_users', function (Blueprint $table) {
            $table->dropColumn('gender');
            $table->dropColumn('mobile');
            $table->dropColumn('is_active');
            $table->dropColumn('last_login_at');
            $table->dropColumn('login_times');
            $table->dropColumn('avatar_id');
            $table->dropColumn('deleted_at');
        });
        Schema::table('z_roles', function (Blueprint $table) {
            $table->dropColumn('label');
        });
        Schema::table('z_permissions', function (Blueprint $table) {
            $table->dropColumn('label');
        });
    }
}
