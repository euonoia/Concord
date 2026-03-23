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
        Schema::table('direct_compensations_hr4', function (Blueprint $table) {
            if (!Schema::hasColumn('direct_compensations_hr4', 'night_diff_pay')) {
                $table->decimal('night_diff_pay', 10, 2)->default(0)->after('overtime_pay')->comment('Night differential pay (10% premium for 22:00-06:00 work)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direct_compensations_hr4', function (Blueprint $table) {
            $table->dropColumn(['night_diff_pay']);
        });
    }
};
