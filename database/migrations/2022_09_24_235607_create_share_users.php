<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateShareUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('share_users', function (Blueprint $table) {
            $table->increments('share_user_id')->comment('共有ユーザー管理テーブルID');
            $table->unsignedBigInteger('requested_user_id')->comment('共有申請したユーザーID');
            $table->unsignedBigInteger('received_user_id')->comment('共有申請されたユーザーID');
            $table->boolean('is_replied')->default(0)->comment('回答の有無 0:未回答 1:回答済');
            $table->boolean('is_shared')->default(0)->comment('共有の有無 0:共有しない 1:共有する');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('requested_user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->foreign('received_user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
        DB::statement("ALTER TABLE `share_users` comment '共有ユーザー管理テーブル'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('share_users');
    }
}
