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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->enum('start_time', ['morning', 'afternoon', 'full'])->default('full');
            $table->date('end_date');
            $table->enum('end_time', ['morning', 'afternoon', 'full'])->default('full');
            $table->decimal('duration', 4, 2); // Example: 0.5, 1.0
            $table->text('reason')->nullable();
            $table->enum('status', ['requested', 'accepted', 'rejected', 'canceled'])->default('requested');
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
        Schema::dropIfExists('leave_requests');
    }
};
