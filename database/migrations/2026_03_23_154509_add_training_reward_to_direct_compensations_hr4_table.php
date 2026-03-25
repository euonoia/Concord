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
            if (!Schema::hasColumn('direct_compensations_hr4', 'training_reward')) {
                $table->decimal('training_reward', 12, 2)->default(0)->after('bonus');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direct_compensations_hr4', function (Blueprint $table) {
            if (Schema::hasColumn('direct_compensations_hr4', 'training_reward')) {
                $table->dropColumn('training_reward');
            }
        });
    }
};
