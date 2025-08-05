<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActionByToOvertimeRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('action_by')->nullable()->after('status');
            $table->foreign('action_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            $table->dropForeign(['action_by']);
            $table->dropColumn('action_by');
        });
    }
}