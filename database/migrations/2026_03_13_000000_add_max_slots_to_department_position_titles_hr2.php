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
            if (!Schema::hasColumn('department_position_titles_hr2', 'max_slots')) {
                $table->integer('max_slots')->unsigned()->nullable()->after('rank_level')->comment('Maximum allowed headcount for this position');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_position_titles_hr2', function (Blueprint $table) {
            if (Schema::hasColumn('department_position_titles_hr2', 'max_slots')) {
                $table->dropColumn('max_slots');
            }
        });
    }
};
