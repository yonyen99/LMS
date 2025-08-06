<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            // Drop old department string column if it exists
            if (Schema::hasColumn('overtime_requests', 'department')) {
                $table->dropColumn('department');
            }

            // Add new department_id foreign key
            $table->unsignedBigInteger('department_id')->after('user_id');

            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            $table->string('department')->nullable();
        });
    }
};
