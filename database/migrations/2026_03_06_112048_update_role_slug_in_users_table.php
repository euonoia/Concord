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
        DB::statement("ALTER TABLE users MODIFY COLUMN role_slug ENUM(
            'sys_super_admin',
            'core_admin',
            'core_employee',
            'hr_admin',
            'hr_employee',
            'logistics_admin',
            'logistics_employee',
            'finance_admin',
            'finance_employee',
            'patient',
            'patient_guardian',
            'admin',
            'doctor',
            'nurse',
            'head_nurse',
            'receptionist',
            'billing'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role_slug ENUM(
            'sys_super_admin',
            'core_admin',
            'core_employee',
            'hr_admin',
            'hr_employee',
            'logistics_admin',
            'logistics_employee',
            'finance_admin',
            'finance_employee',
            'patient',
            'patient_guardian'
        ) NOT NULL");
    }
};
