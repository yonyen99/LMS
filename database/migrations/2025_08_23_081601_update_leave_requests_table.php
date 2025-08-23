<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Drop the old reason column
            $table->dropColumn('reason');
            // Modify existing enum columns
            $table->enum('start_time', ['morning', 'afternoon'])->default('morning')->change();
            $table->enum('end_time', ['morning', 'afternoon'])->default('afternoon')->change();
            $table->enum('status', ['planned', 'requested', 'accepted', 'rejected', 'canceled'])->default('requested')->change();
            // Add new reason_type and other_reason columns
            $table->enum('reason_type', ['Personal', 'Medical', 'Family', 'Vacation', 'Other'])->nullable()->after('duration');
            $table->text('other_reason')->nullable()->after('reason_type');
        });
    }

    public function down()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Reverse changes: restore reason, revert enums, drop new columns
            $table->text('reason')->nullable()->after('duration');
            $table->enum('start_time', ['morning', 'afternoon', 'full'])->default('full')->change();
            $table->enum('end_time', ['morning', 'afternoon', 'full'])->default('full')->change();
            $table->enum('status', ['requested', 'accepted', 'rejected', 'canceled'])->default('requested')->change();
            $table->dropColumn(['reason_type', 'other_reason']);
        });
    }
};