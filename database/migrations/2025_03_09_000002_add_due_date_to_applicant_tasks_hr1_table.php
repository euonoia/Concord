<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('applicant_tasks_hr1')) {
            Schema::table('applicant_tasks_hr1', function (Blueprint $table) {
                if (!Schema::hasColumn('applicant_tasks_hr1', 'due_date')) {
                    $table->date('due_date')->nullable()->after('job_posting_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('applicant_tasks_hr1') && Schema::hasColumn('applicant_tasks_hr1', 'due_date')) {
            Schema::table('applicant_tasks_hr1', function (Blueprint $table) {
                $table->dropColumn('due_date');
            });
        }
    }
};
