<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recognitions_hr1', function (Blueprint $table) {
            $table->id();
            $table->string('from');
            $table->string('to');
            $table->text('reason');
            $table->string('award_type');
            $table->date('date');
            $table->integer('congratulations')->default(0);
            $table->integer('boosts')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recognitions_hr1');
    }
};

