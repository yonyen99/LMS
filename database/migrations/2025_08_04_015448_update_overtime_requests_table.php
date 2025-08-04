<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            $table->dropColumn('department');
            $table->foreignId('department_id')->after('user_id')->constrained()->onDelete('cascade')->index();
            $table->enum('status', ['requested', 'approved', 'rejected', 'cancelled'])->default('requested')->change();
            $table->dropColumn('read_at');
        });
    }

    public function down(): void
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            $table->string('department')->after('user_id');
            $table->dropColumn('department_id');
            $table->enum('status', ['requested', 'approved', 'rejected', 'canceled'])->default('requested')->change();
            $table->timestamp('read_at')->nullable()->after('last_changed_at');
        });
    }
};