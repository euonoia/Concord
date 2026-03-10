<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users_hr1', function (Blueprint $table) {
            $table->string('employee_id', 50)->nullable()->unique()->after('id');
        });

        // Backfill employee_id for existing users (yyyymmdd + 5-digit unique)
        $users = DB::table('users_hr1')->whereNull('employee_id')->get();
        $used = [];
        foreach ($users as $user) {
            $datePart = isset($user->created_at) ? date('Ymd', strtotime($user->created_at)) : now()->format('Ymd');
            do {
                $rand = str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
                $employeeId = $datePart . $rand;
            } while (in_array($employeeId, $used, true));
            $used[] = $employeeId;
            DB::table('users_hr1')->where('id', $user->id)->update(['employee_id' => $employeeId]);
        }
    }

    public function down(): void
    {
        Schema::table('users_hr1', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
    }
};
