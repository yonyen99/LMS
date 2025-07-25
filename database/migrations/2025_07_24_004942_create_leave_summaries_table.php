<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('leave_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('report_date');

            $table->decimal('entitled', 5, 2)->default(0);
            $table->decimal('taken', 5, 2)->default(0);
            $table->decimal('planned', 5, 2)->default(0);
            $table->decimal('requested', 5, 2)->default(0);
            $table->decimal('available_actual', 5, 2)->default(0);
            $table->decimal('available_simulated', 5, 2)->default(0);

            $table->timestamps();
            $table->unique(['user_id', 'leave_type_id', 'report_date'], 'user_leave_date_unique');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('leave_summaries');
    }
};
