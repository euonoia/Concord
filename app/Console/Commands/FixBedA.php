<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixBedA extends Command
{
    protected $signature = 'core1:fix-bed-a';
    protected $description = 'Move admission from Bed-A to WARD-01 and delete Bed-A';

    public function handle(): int
    {
        // 1. Find Bed-A
        $bedA = DB::table('beds_core1')->where('bed_number', 'Bed-A')->first();
        if (!$bedA) {
            $this->error('Bed-A not found.');
            return 1;
        }
        $this->info("Found Bed-A: id={$bedA->id}, room_id={$bedA->room_id}, status={$bedA->status}");

        // 2. Find active admission on Bed-A
        $admission = DB::table('admissions_core1')
            ->where('bed_id', $bedA->id)
            ->where('status', 'Admitted')
            ->first();
        $this->info('Admission on Bed-A: ' . ($admission ? "id={$admission->id}" : 'none'));

        // 3. Find WARD-01 bed
        $ward01 = DB::table('beds_core1')->where('bed_number', 'WARD-01')->first();
        if (!$ward01) {
            $this->error('WARD-01 not found.');
            return 1;
        }
        $this->info("Found WARD-01: id={$ward01->id}, status={$ward01->status}");

        DB::transaction(function () use ($bedA, $admission, $ward01) {
            // 4. Move admission to WARD-01
            if ($admission) {
                DB::table('admissions_core1')
                    ->where('id', $admission->id)
                    ->update(['bed_id' => $ward01->id]);
                $this->info("Moved admission {$admission->id} to WARD-01.");
            }

            // 5. Set WARD-01 → Occupied
            DB::table('beds_core1')
                ->where('id', $ward01->id)
                ->update(['status' => 'Occupied', 'updated_at' => now()]);
            $this->info('WARD-01 set to Occupied.');

            // 6. Delete Bed-A
            DB::table('beds_core1')->where('id', $bedA->id)->delete();
            $this->info('Bed-A deleted.');

            // 7. Delete empty test room
            $roomBedsLeft = DB::table('beds_core1')->where('room_id', $bedA->room_id)->count();
            if ($roomBedsLeft === 0) {
                $room = DB::table('rooms_core1')->where('id', $bedA->room_id)->first();
                DB::table('rooms_core1')->where('id', $bedA->room_id)->delete();
                $this->info('Deleted empty test room: ' . ($room ? $room->room_number : $bedA->room_id));

                if ($room) {
                    $wardRoomsLeft = DB::table('rooms_core1')->where('ward_id', $room->ward_id)->count();
                    if ($wardRoomsLeft === 0) {
                        $ward = DB::table('wards_core1')->where('id', $room->ward_id)->first();
                        DB::table('wards_core1')->where('id', $room->ward_id)->delete();
                        $this->info('Deleted empty test ward: ' . ($ward ? $ward->name : $room->ward_id));
                    }
                }
            }
        });

        $this->info('Done.');
        return 0;
    }
}
