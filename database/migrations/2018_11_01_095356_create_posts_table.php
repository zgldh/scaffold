<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->unique()->comment('标题');
            $table->text('content')->comment('内容');
            $table->string('password')->nullable()->comment('阅读密码');
            $table->string('email')->nullable()->comment('反馈邮箱');
            $table->string('category', 15)->nullable()->comment('分类');
            $table->integer('status')->nullable()->default(1)->comment('状态');
            $table->integer('created_by')->unsigned()->nullable()->comment('创建者');
            $table->softDeletes('deleted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('posts');
    }
}
