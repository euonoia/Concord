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
        Schema::create('available_jobs_hr4', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department');
            $table->text('description');
            $table->text('requirements');
            $table->string('salary_range')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->bigInteger('posted_by');
            $table->timestamp('posted_at')->useCurrent();
            $table->timestamps();

            $table->foreign('posted_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('available_jobs_hr4');
    }
};
