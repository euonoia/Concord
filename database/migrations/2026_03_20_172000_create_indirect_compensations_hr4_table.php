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
        Schema::create('indirect_compensations_hr4', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('month'); // YYYY-MM
            $table->string('benefit_name');
            $table->decimal('amount', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indirect_compensations_hr4');
    }
};
