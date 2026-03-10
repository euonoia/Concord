<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_criteria_hr1', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->enum('section', ['A', 'B', 'C']);
            $table->integer('weight')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_criteria_hr1');
    }
};

