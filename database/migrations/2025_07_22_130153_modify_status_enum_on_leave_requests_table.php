<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE leave_requests MODIFY status ENUM('planned', 'requested', 'approved', 'accepted', 'rejected', 'cancellation', 'canceled') NOT NULL DEFAULT 'requested'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE leave_requests MODIFY status ENUM('requested', 'accepted', 'rejected', 'canceled') NOT NULL DEFAULT 'requested'");
    }
};
