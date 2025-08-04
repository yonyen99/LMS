<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypicalAnnualRequestsToLeaveTypesTable extends Migration
{
    public function up()
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->string('typical_annual_requests')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn('typical_annual_requests');
        });
    }
}