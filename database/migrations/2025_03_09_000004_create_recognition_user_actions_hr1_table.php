<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recognition_user_actions_hr1', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('recognition_id');
            $table->boolean('congratulated')->default(false);
            $table->boolean('boosted')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'recognition_id']);
            $table->foreign('user_id')->references('id')->on('users_hr1')->onDelete('cascade');
            $table->foreign('recognition_id')->references('id')->on('recognitions_hr1')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recognition_user_actions_hr1');
    }
};
