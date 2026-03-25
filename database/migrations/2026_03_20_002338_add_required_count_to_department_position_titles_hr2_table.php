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
        Schema::table('department_position_titles_hr2', function (Blueprint $table) {
            $table->integer('required_count')->nullable()->default(0)->after('position_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_position_titles_hr2', function (Blueprint $table) {
            $table->dropColumn('required_count');
        });
    }
};
