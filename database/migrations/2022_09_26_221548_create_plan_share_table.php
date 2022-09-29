<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePlanShareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_share', function (Blueprint $table) {
            $table->increments('plan_share_id')->comment('予定共有管理テーブルID');
            $table->unsignedBigInteger('plan_id')->comment('予定ID');
            $table->unsignedBigInteger('plan_made_user_id')->comment('予定を作成したユーザーID');
            $table->unsignedBigInteger('plan_shared_user_id')->comment('予定を共有されたユーザーID');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('plan_id')
                ->references('id')
                ->on('plans')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->foreign('plan_made_user_id')
                ->references('user_id')
                ->on('plans')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->foreign('plan_shared_user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
        DB::statement("ALTER TABLE `plan_share` comment '予定共有管理テーブル'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_share');
    }
}
