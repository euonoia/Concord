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
        if (!Schema::hasTable('competencies_hr2')) {
            Schema::create('competencies_hr2', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('competency_group')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competencies_hr2');
    }
};
