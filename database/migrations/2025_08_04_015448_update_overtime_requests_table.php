<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check and drop 'department' column if it exists
        if (Schema::hasColumn('overtime_requests', 'department')) {
            Schema::table('overtime_requests', function (Blueprint $table) {
                $table->dropColumn('department');
            });
        }

        // Check and drop 'read_at' column if it exists
        if (Schema::hasColumn('overtime_requests', 'read_at')) {
            Schema::table('overtime_requests', function (Blueprint $table) {
                $table->dropColumn('read_at');
            });
        }

        // Add/modify columns
        Schema::table('overtime_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('overtime_requests', 'department_id')) {
                $table->foreignId('department_id')
                      ->after('user_id')
                      ->constrained()
                      ->onDelete('cascade')
                      ->index();
            }

            $table->enum('status', ['requested', 'approved', 'rejected', 'cancelled'])
                  ->default('requested')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('overtime_requests', 'department')) {
                $table->string('department')->after('user_id');
            }

            if (Schema::hasColumn('overtime_requests', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }

            $table->enum('status', ['requested', 'approved', 'rejected', 'canceled'])
                  ->default('requested')
                  ->change();

            if (!Schema::hasColumn('overtime_requests', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('last_changed_at');
            }
        });
    }
};
