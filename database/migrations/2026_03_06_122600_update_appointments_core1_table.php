<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update ENUM for status (TiDB compatible statement)
        DB::statement("ALTER TABLE appointments_core1 MODIFY COLUMN status ENUM(
            'pending',
            'scheduled',
            'confirmed',
            'approved',
            'rejected',
            'completed',
            'cancelled',
            'no-show'
        ) NOT NULL DEFAULT 'scheduled'");

        // 2. Add missing columns for tracking
        Schema::table('appointments_core1', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments_core1', 'approved_by')) {
                $table->bigInteger('approved_by')->unsigned()->nullable()->after('status');
            }
            if (!Schema::hasColumn('appointments_core1', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('appointments_core1', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments_core1', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'approved_at', 'rejection_reason']);
        });

        DB::statement("ALTER TABLE appointments_core1 MODIFY COLUMN status ENUM(
            'scheduled',
            'confirmed',
            'completed',
            'cancelled',
            'no-show'
        ) NOT NULL DEFAULT 'scheduled'");
    }
};
