<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobPostingsHr1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $postings = [
            // Residency
            [
                'title' => 'Internal Medicine Residency',
                'track_type' => 'residency',
                'dept_code' => 'MED-GEN',
                'needed_applicants' => 12,
                'description' => 'A comprehensive 3-year program focusing on adult diseases, diagnostics, and long-term care management.',
                'is_active' => 1,
            ],
            [
                'title' => 'Pediatrics Residency',
                'track_type' => 'residency',
                'dept_code' => 'PED-01',
                'needed_applicants' => 8,
                'description' => 'Dedicated to the physical, emotional, and social health of children from birth to young adulthood.',
                'is_active' => 1,
            ],
            [
                'title' => 'Neurology Residency',
                'track_type' => 'residency',
                'dept_code' => 'NEURO-01',
                'needed_applicants' => 5,
                'description' => 'Comprehensive training in the diagnosis and management of nervous system disorders.',
                'is_active' => 1,
            ],
            [
                'title' => 'Psychiatry Residency',
                'track_type' => 'residency',
                'dept_code' => 'PSY-01',
                'needed_applicants' => 4,
                'description' => 'In-depth clinical experience focusing on mental health, psychotherapy, and psychopharmacology.',
                'is_active' => 1,
            ],
            // Fellowship
            [
                'title' => 'Cardiovascular Disease Fellowship',
                'track_type' => 'fellowship',
                'dept_code' => 'MED-GEN',
                'needed_applicants' => 2,
                'description' => 'Advanced training in clinical cardiology, echocardiography, and invasive procedures.',
                'is_active' => 1,
            ],
            [
                'title' => 'Gastroenterology Fellowship',
                'track_type' => 'fellowship',
                'dept_code' => 'MED-GEN',
                'needed_applicants' => 0, // High Demand
                'description' => 'Specialized training in digestive system disorders, endoscopy, and hepatology.',
                'is_active' => 1,
            ],
            [
                'title' => 'Pulmonary and Critical Care',
                'track_type' => 'fellowship',
                'dept_code' => 'MED-GEN',
                'needed_applicants' => 3,
                'description' => 'Dual training in managing severe respiratory diseases and life-threatening conditions in the ICU.',
                'is_active' => 1,
            ],
            [
                'title' => 'Pediatric Neurology Fellowship',
                'track_type' => 'fellowship',
                'dept_code' => 'PED-01',
                'needed_applicants' => 2,
                'description' => 'Specialized training in neurology tailored specifically for pediatric patients.',
                'is_active' => 1,
            ]
        ];

        foreach ($postings as $posting) {
            DB::table('job_postings_hr1')->updateOrInsert(
                ['title' => $posting['title'], 'dept_code' => $posting['dept_code']],
                $posting
            );
        }
    }
}
