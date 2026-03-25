<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FloorMapSeeder extends Seeder
{
    /**
     * Seeds 4 hospital zones with 2 rooms × 5 beds = 10 beds per zone.
     * Uses INSERT IGNORE so it is idempotent on re-run.
     */
    public function run(): void
    {
        $zones = [
            [
                'ward_type'   => 'ICU',
                'name'        => 'Intensive Care Unit',
                'description' => 'Critical care ward for seriously ill patients',
                'capacity'    => 10,
                'rooms' => [
                    ['room_number' => 'ICU-101', 'room_type' => 'Critical Care'],
                    ['room_number' => 'ICU-102', 'room_type' => 'Critical Care'],
                ],
            ],
            [
                'ward_type'   => 'ER',
                'name'        => 'Emergency Room',
                'description' => 'Emergency and acute care unit',
                'capacity'    => 10,
                'rooms' => [
                    ['room_number' => 'ER-101', 'room_type' => 'Emergency'],
                    ['room_number' => 'ER-102', 'room_type' => 'Emergency'],
                ],
            ],
            [
                'ward_type'   => 'WARD',
                'name'        => 'General Ward',
                'description' => 'Standard inpatient ward for general admissions',
                'capacity'    => 10,
                'rooms' => [
                    ['room_number' => 'GW-101', 'room_type' => 'Standard'],
                    ['room_number' => 'GW-102', 'room_type' => 'Standard'],
                ],
            ],
            [
                'ward_type'   => 'OR',
                'name'        => 'Operating Room',
                'description' => 'Surgical suites and post-op recovery',
                'capacity'    => 10,
                'rooms' => [
                    ['room_number' => 'OR-101', 'room_type' => 'Surgical Suite'],
                    ['room_number' => 'OR-102', 'room_type' => 'Recovery'],
                ],
            ],
        ];

        $now = now();

        foreach ($zones as $zone) {
            // Upsert ward (skip if name already exists)
            $wardId = DB::table('wards_core1')
                ->where('name', $zone['name'])
                ->value('id');

            if (!$wardId) {
                $wardId = DB::table('wards_core1')->insertGetId([
                    'name'        => $zone['name'],
                    'description' => $zone['description'],
                    'capacity'    => $zone['capacity'],
                    'ward_type'   => $zone['ward_type'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            } else {
                // Ensure existing ward has correct ward_type
                DB::table('wards_core1')
                    ->where('id', $wardId)
                    ->update(['ward_type' => $zone['ward_type'], 'updated_at' => $now]);
            }

            $bedNumber = 1;
            foreach ($zone['rooms'] as $roomDef) {
                // Upsert room
                $roomId = DB::table('rooms_core1')
                    ->where('ward_id', $wardId)
                    ->where('room_number', $roomDef['room_number'])
                    ->value('id');

                if (!$roomId) {
                    $roomId = DB::table('rooms_core1')->insertGetId([
                        'ward_id'     => $wardId,
                        'room_number' => $roomDef['room_number'],
                        'room_type'   => $roomDef['room_type'],
                        'status'      => 'Active',
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ]);
                }

                // Insert 5 beds per room (10 total per zone)
                for ($i = 1; $i <= 5; $i++) {
                    $bedLabel = $zone['ward_type'] . '-' . str_pad($bedNumber, 2, '0', STR_PAD_LEFT);
                    $exists = DB::table('beds_core1')
                        ->where('room_id', $roomId)
                        ->where('bed_number', $bedLabel)
                        ->exists();

                    if (!$exists) {
                        DB::table('beds_core1')->insert([
                            'room_id'    => $roomId,
                            'bed_number' => $bedLabel,
                            'status'     => 'Available',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }

                    $bedNumber++;
                }
            }
        }
    }
}
