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
        if (!Schema::hasTable('payroll_request_hr2')) {
            return;
        }

        Schema::table('payroll_request_hr2', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_request_hr2', 'salary')) {
                $table->decimal('salary', 12, 2)->nullable()->after('details')->comment('Gross Salary requested');
            }
            if (!Schema::hasColumn('payroll_request_hr2', 'net_pay')) {
                $table->decimal('net_pay', 12, 2)->nullable()->after('salary')->comment('Net pay amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('payroll_request_hr2')) {
            return;
        }

        Schema::table('payroll_request_hr2', function (Blueprint $table) {
            if (Schema::hasColumn('payroll_request_hr2', 'net_pay')) {
                $table->dropColumn('net_pay');
            }
            // keep salary, because it's likely in use elsewhere; avoid dropping automatically when existing data
        });
    }
};
