<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('question_sets_hr1')) {
            Schema::table('question_sets_hr1', function (Blueprint $table) {
                if (!Schema::hasColumn('question_sets_hr1', 'job_posting_id')) {
                    $table->unsignedBigInteger('job_posting_id')->nullable()->after('created_by');
                    $table->foreign('job_posting_id')->references('id')->on('job_postings_hr1')->onDelete('set null');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('question_sets_hr1') && Schema::hasColumn('question_sets_hr1', 'job_posting_id')) {
            Schema::table('question_sets_hr1', function (Blueprint $table) {
                $table->dropForeign(['job_posting_id']);
                $table->dropColumn('job_posting_id');
            });
        }
    }
};
