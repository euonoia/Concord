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
        Schema::create('training_rewards_hr4', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('training_name');
            $table->decimal('grade', 5, 2); // e.g., 95.50 for percentage
            $table->decimal('reward_amount', 10, 2)->default(0);
            $table->date('training_date');
            $table->string('month'); // YYYY-MM format for compensation period
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index for performance
            $table->index(['employee_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_rewards_hr4');
    }
};
