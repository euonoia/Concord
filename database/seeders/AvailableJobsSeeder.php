<?php

namespace Database\Seeders;

use App\Models\admin\Hr\hr4\AvailableJob;
use App\Models\User;
use Illuminate\Database\Seeder;

class AvailableJobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role_slug', 'admin_hr4')->first();

        if (!$admin) {
            // Create a sample admin if none exists
            $admin = User::create([
                'username' => 'hr4_admin',
                'email' => 'hr4@company.com',
                'password' => bcrypt('password'),
                'role_slug' => 'admin_hr4',
            ]);
        }

        $sampleJobs = [
            [
                'title' => 'Software Developer',
                'department' => 'IT',
                'specialization_name' => 'Web Development',
                'position_id' => 1,
                'description' => 'Develop and maintain web applications using modern technologies.',
                'requirements' => 'PHP, Laravel, JavaScript, MySQL experience required.',
                'salary_range' => '₱30,000 - ₱50,000',
                'positions_available' => 3,
                'status' => 'open',
                'posted_by' => $admin->id,
            ],
            [
                'title' => 'HR Specialist',
                'department' => 'Human Resources',
                'specialization_name' => 'Recruitment',
                'position_id' => 2,
                'description' => 'Manage recruitment processes and employee relations.',
                'requirements' => 'Bachelor\'s degree in HR or related field, 2+ years experience.',
                'salary_range' => '₱25,000 - ₱35,000',
                'positions_available' => 2,
                'status' => 'open',
                'posted_by' => $admin->id,
            ],
            [
                'title' => 'Accountant',
                'department' => 'Finance',
                'specialization_name' => 'Financial Reporting',
                'position_id' => 3,
                'description' => 'Handle financial reporting and accounting tasks.',
                'requirements' => 'CPA certification preferred, Excel proficiency required.',
                'salary_range' => '₱28,000 - ₱40,000',
                'positions_available' => 1,
                'status' => 'open',
                'posted_by' => $admin->id,
            ],
            [
                'title' => 'Marketing Coordinator',
                'department' => 'Marketing',
                'specialization_name' => 'Digital Marketing',
                'position_id' => 4,
                'description' => 'Coordinate marketing campaigns and social media activities.',
                'requirements' => 'Marketing degree, social media experience preferred.',
                'salary_range' => '₱22,000 - ₱30,000',
                'positions_available' => 2,
                'status' => 'open',
                'posted_by' => $admin->id,
            ],
            [
                'title' => 'Operations Manager',
                'department' => 'Operations',
                'specialization_name' => 'Project Management',
                'position_id' => 5,
                'description' => 'Oversee daily operations and project management.',
                'requirements' => '5+ years management experience, PMP certification a plus.',
                'salary_range' => '₱45,000 - ₱60,000',
                'positions_available' => 1,
                'status' => 'open',
                'posted_by' => $admin->id,
            ],
        ];

        foreach ($sampleJobs as $job) {
            AvailableJob::create($job);
        }
    }
}
