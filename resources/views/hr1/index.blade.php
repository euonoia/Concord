<?php

$employee = [
    'first_name' => 'Employee',
    'last_name'  => '',
    'role'       => 'User'
];

$counts = [
    'Competencies' => 0,
    'Courses'      => 0,
    'Trainings'    => 0,
    'ESS Requests' => 0
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - HR1</title>
        <link rel="stylesheet" href="{{ asset('css/hr1/example.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
 <div style="margin-top: 2rem; padding: 2rem; background: #f8f9fa; border-radius: 12px;">
            <h3 style="margin-bottom: 1.5rem; color: #333;">HR1 Management System - View Dashboards</h3>
            <p style="margin-bottom: 1.5rem; color: #666;">Click below to view the role-based dashboards:</p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <a href="{{ url('/dashboard_hr1?role=admin') }}" 
                   style="display: block; padding: 1.5rem; background: #1B3C53; color: white; text-decoration: none; border-radius: 8px; text-align: center; transition: transform 0.2s;"
                   onmouseover="this.style.transform='scale(1.05)'" 
                   onmouseout="this.style.transform='scale(1)'">
                    <i class="bi bi-shield-check" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                    <strong>Admin Dashboard</strong>
                    <p style="margin-top: 0.5rem; font-size: 0.9rem; opacity: 0.9;">Complete HR management overview</p>
                </a>

                <a href="{{ url('/dashboard_hr1?role=staff') }}" 
                   style="display: block; padding: 1.5rem; background: #2C5F7C; color: white; text-decoration: none; border-radius: 8px; text-align: center; transition: transform 0.2s;"
                   onmouseover="this.style.transform='scale(1.05)'" 
                   onmouseout="this.style.transform='scale(1)'">
                    <i class="bi bi-people" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                    <strong>Staff Dashboard</strong>
                    <p style="margin-top: 0.5rem; font-size: 0.9rem; opacity: 0.9;">HR staff management view</p>
                </a>

                <a href="{{ url('/dashboard_hr1?role=candidate') }}" 
                   style="display: block; padding: 1.5rem; background: #3D7BA5; color: white; text-decoration: none; border-radius: 8px; text-align: center; transition: transform 0.2s;"
                   onmouseover="this.style.transform='scale(1.05)'" 
                   onmouseout="this.style.transform='scale(1)'">
                    <i class="bi bi-person-badge" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                    <strong>Candidate Dashboard</strong>
                    <p style="margin-top: 0.5rem; font-size: 0.9rem; opacity: 0.9;">Job applicant view</p>
                </a>
            </div>
        </div>

</body>
</html>
