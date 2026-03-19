<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleEmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, set required counts for some positions
        $positions = DepartmentPositionTitle::all();
        foreach ($positions as $position) {
            if ($position->id <= 5) { // Set requirements for first 5 positions
                $position->update(['required_count' => rand(3, 6)]); // Higher requirements to show needs
            }
        }

        // Create sample users and employees
        $sampleData = [
            [
                'username' => 'john.doe',
                'email' => 'john.doe@company.com',
                'employee_id' => 'EMP001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'department_id' => 'MED-GEN', // Use existing department
                'position_id' => 1,
                'hire_date' => '2024-01-15',
            ],
            [
                'username' => 'jane.smith',
                'email' => 'jane.smith@company.com',
                'employee_id' => 'EMP002',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'department_id' => 'PED-01',
                'position_id' => 2,
                'hire_date' => '2024-02-01',
            ],
            [
                'username' => 'bob.johnson',
                'email' => 'bob.johnson@company.com',
                'employee_id' => 'EMP003',
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'department_id' => 'PSY-01',
                'position_id' => 3,
                'hire_date' => '2024-03-10',
            ],
            [
                'username' => 'alice.brown',
                'email' => 'alice.brown@company.com',
                'employee_id' => 'EMP004',
                'first_name' => 'Alice',
                'last_name' => 'Brown',
                'department_id' => 'MED-GEN',
                'position_id' => 1,
                'hire_date' => '2024-04-05',
            ],
            [
                'username' => 'charlie.wilson',
                'email' => 'charlie.wilson@company.com',
                'employee_id' => 'EMP005',
                'first_name' => 'Charlie',
                'last_name' => 'Wilson',
                'department_id' => 'RAD-01',
                'position_id' => 4,
                'hire_date' => '2024-05-20',
            ],
        ];

        foreach ($sampleData as $data) {
            // Check if user exists, if not create
            $user = User::where('username', $data['username'])->first();
            if (!$user) {
                $user = User::create([
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => bcrypt('password'),
                    'role_slug' => 'employee',
                ]);
            }

            // Check if employee exists
            $existingEmployee = Employee::where('employee_id', $data['employee_id'])->first();
            if (!$existingEmployee) {
                // Create employee linked to user
                Employee::create([
                    'user_id' => $user->id,
                    'employee_id' => $data['employee_id'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'department_id' => $data['department_id'],
                    'position_id' => $data['position_id'],
                    'hire_date' => $data['hire_date'],
                    'is_on_duty' => 1,
                ]);
            }
        }
    }
}
