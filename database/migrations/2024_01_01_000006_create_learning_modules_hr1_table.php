<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_modules_hr1', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('user_learning_modules_hr1', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users_hr1')->onDelete('cascade');
            $table->foreignId('learning_module_id')->constrained('learning_modules_hr1')->onDelete('cascade');
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_learning_modules_hr1');
        Schema::dropIfExists('learning_modules_hr1');
    }
};

