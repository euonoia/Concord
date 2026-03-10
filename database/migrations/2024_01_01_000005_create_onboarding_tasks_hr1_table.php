<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_tasks_hr1', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department');
            $table->enum('category', ['Pre-onboarding', 'Orientation', 'IT Setup', 'Training']);
            $table->boolean('completed')->default(false);
            $table->enum('assigned_to', ['admin', 'staff', 'candidate'])->default('candidate');
            $table->foreignId('user_id')->nullable()->constrained('users_hr1')->onDelete('cascade');
            $table->integer('required_for_phase')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_tasks_hr1');
    }
};

