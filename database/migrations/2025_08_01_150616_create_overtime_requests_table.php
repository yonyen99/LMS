<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('department');
            $table->date('overtime_date');
            $table->enum('time_period', ['before_shift', 'after_shift', 'weekend', 'holiday'])->default('after_shift');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('duration', 4, 2); 
            $table->text('reason')->nullable();
            $table->enum('status', ['requested', 'approved', 'rejected', 'canceled'])->default('requested');
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('last_changed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_requests');
    }
};
