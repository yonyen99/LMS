<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class UpdateLeaveRequestsStatusEnum extends Migration
{
    public function up(): void
    {
        // Update ENUM field to include 'planned'
        DB::statement("ALTER TABLE leave_requests MODIFY status ENUM('planned', 'requested', 'approved', 'rejected') NOT NULL");
    }

    public function down(): void
    {
        // Rollback ENUM to previous values (if needed)
        DB::statement("ALTER TABLE leave_requests MODIFY status ENUM('requested', 'approved', 'rejected') NOT NULL");
    }
}