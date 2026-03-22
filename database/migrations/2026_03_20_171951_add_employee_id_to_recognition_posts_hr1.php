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
        Schema::table('recognition_posts_hr1', function (Blueprint $table) {
            $table->string('employee_id')->nullable()->after('admin_id');
            // Loose relationship since employee_id is a string and not a standard ID
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recognition_posts_hr1', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
    }
};
