<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnsIdToShareRequestIdOnShareRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('share_requests', function (Blueprint $table) {
            $table->renameColumn('id', 'share_request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('share_requests', function (Blueprint $table) {
            $table->renameColumn('share_request_id', 'id');
        });
    }
}
