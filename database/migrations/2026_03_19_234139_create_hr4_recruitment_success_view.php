<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE VIEW hr4_recruitment_success AS
            SELECT
                h.id AS hired_id,
                h.hr4_job_id,
                h.employee_id,
                h.full_name,
                h.hired_at,
                j.title AS job_title,
                j.department,
                j.specialization_name,
                j.position_id,
                j.status
            FROM hired_users_hr4 h
            LEFT JOIN available_jobs_hr4 j ON h.hr4_job_id = j.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS hr4_recruitment_success");
    }
};
