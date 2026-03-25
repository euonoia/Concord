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
        // Update Enum in onboarding_assessments_hr1
        // Note: In MySQL, we need to use a raw statement to change enum values if they are restricted
        DB::statement("ALTER TABLE onboarding_assessments_hr1 MODIFY COLUMN assessment_status ENUM('pending', 'scheduled', 'passed', 'failed', 'assessed') DEFAULT 'pending'");

        // Add assessed_by and validated_by to onboarding_assessments_hr1 if they don't exist
        Schema::table('onboarding_assessments_hr1', function (Blueprint $table) {
            if (!Schema::hasColumn('onboarding_assessments_hr1', 'assessed_by')) {
                $table->string('assessed_by', 150)->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('onboarding_assessments_hr1', 'is_validated')) {
                $table->boolean('is_validated')->default(false)->after('assessed_by');
            }
            if (!Schema::hasColumn('onboarding_assessments_hr1', 'validated_by')) {
                $table->string('validated_by', 150)->nullable()->after('is_validated');
            }
        });

        // Ensure onboarding_assessment_scores_hr1 has the required columns
        if (Schema::hasTable('onboarding_assessment_scores_hr1')) {
            Schema::table('onboarding_assessment_scores_hr1', function (Blueprint $table) {
                if (!Schema::hasColumn('onboarding_assessment_scores_hr1', 'assessed_by')) {
                    $table->string('assessed_by', 150)->nullable()->after('remarks');
                }
                if (!Schema::hasColumn('onboarding_assessment_scores_hr1', 'validated_by')) {
                    $table->string('validated_by', 150)->nullable()->after('assessed_by');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboarding_assessments_hr1', function (Blueprint $table) {
            $table->dropColumn(['assessed_by', 'is_validated', 'validated_by']);
        });
        
        DB::statement("ALTER TABLE onboarding_assessments_hr1 MODIFY COLUMN assessment_status ENUM('pending', 'scheduled', 'passed', 'failed') DEFAULT 'pending'");

        if (Schema::hasTable('onboarding_assessment_scores_hr1')) {
            Schema::table('onboarding_assessment_scores_hr1', function (Blueprint $table) {
                $table->dropColumn(['assessed_by', 'validated_by']);
            });
        }
    }
};
