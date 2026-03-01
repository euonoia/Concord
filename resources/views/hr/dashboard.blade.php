@extends('layouts.dashboard.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="dashboard-wrapper" style="padding: 2.5rem 1.5rem; max-width: 1300px; margin: 0 auto; font-family: 'Inter', sans-serif; background-color: #f8fafc; min-height: 100vh;">
    
    <div class="header-section" style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <h1 style="color: #1a202c; font-size: 1.875rem; font-weight: 700; margin: 0; letter-spacing: -0.025em;">
                Welcome back, {{ $employee->first_name }}!
            </h1>
            <p style="color: #64748b; font-size: 1rem; margin-top: 0.5rem;">
                Here’s what’s happening with your development profile today.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="{{ route('user.attendance.scan') }}" 
               title="Scan Attendance"
               style="display: flex; align-items: center; justify-content: center; background: #2563eb; color: white; width: 48px; height: 48px; border-radius: 12px; text-decoration: none; transition: all 0.2s; box-shadow: 0 4px 12px rgba(37,99,235,0.2);"
               onmouseover="this.style.backgroundColor='#1d4ed8'; this.style.transform='translateY(-2px)'" 
               onmouseout="this.style.backgroundColor='#2563eb'; this.style.transform='translateY(0)'">
                <i class="fas fa-qrcode" style="font-size: 1.25rem;"></i>
            </a>

            <div class="date-chip" style="background: #ffffff; padding: 0.8rem 1.2rem; border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem; font-weight: 600; box-shadow: 0 1px 2px rgba(0,0,0,0.05); height: 48px; display: flex; align-items: center;">
                <i class="far fa-calendar-alt" style="margin-right: 8px;"></i> {{ date('F j, Y') }}
            </div>
        </div>
    </div>

    <div class="dashboard_grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
        
        <div class="dashboard_card" style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); overflow: hidden;">
            <div style="background: #f1f5f9; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="color: #475569; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-id-card" style="font-size: 1.1rem;"></i>
                    </div>
                    <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 600;">Personnel Profile</h3>
                </div>
                <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 700;">Verified</span>
            </div>

            <div style="padding: 1.5rem;">
                <div class="info-row" style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color:#374151; font-size: 0.9rem; font-weight: 500;">EMPLOYEE ID</span>
                        <span style="color: #1e293b; font-weight: 600; background: #f8fafc; padding: 4px 12px; border-radius: 6px; border: 1px solid #f1f5f9;">
                            #{{ $employee->employee_id ?? 'PENDING' }}
                        </span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color:#374151; font-size: 0.9rem; font-weight: 500;">FULL NAME</span>
                        <span style="color: #1e293b; font-weight: 600;">{{ $employee->first_name }} {{ $employee->last_name }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #374151; font-size: 0.9rem; font-weight: 500;">DEPARTMENT</span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></span>
                            <span style="color: #1e293b; font-weight: 600;">{{ $employee->department_id ?? 'General Operations' }}</span>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color:#374151; font-size: 0.9rem; font-weight: 500;">SPECIALIZATION</span>
                        <span style="color: #6366f1; font-weight: 600; background: #eef2ff; padding: 4px 12px; border-radius: 6px;">
                            {{ $employee->specialization ?? 'Unassigned' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard_card" style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 2rem;">
                    <div style="color: #ea580c; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-rocket" style="font-size: 1.1rem;"></i>
                    </div>
                    <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 600;">Quick Actions</h3>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <a href="{{ route('user.training.index') }}" style="transition: all 0.2s; background-color: #2B5F6B; color: white; padding: 1rem; border-radius: 10px; text-decoration: none; font-weight: 600; display: flex; align-items: center; justify-content: space-between; group">
                        <span style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-graduation-cap"></i> Browse Available Trainings
                        </span>
                        <i class="fas fa-chevron-right" style="font-size: 0.8rem; opacity: 0.7;"></i>
                    </a>

                    <a href="{{ route('user.learning.index') }}" style="transition: all 0.2s; background-color: #374151; color: white; padding: 1rem; border-radius: 10px; text-decoration: none; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                        <span style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-project-diagram"></i> My Learning Path
                        </span>
                        <i class="fas fa-chevron-right" style="font-size: 0.8rem; opacity: 0.7;"></i>
                    </a>
                </div>
            </div>

            <div style="margin-top: 2rem; padding: 1rem; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1; text-align: center;">
                <p style="margin: 0; font-size: 0.85rem; color: #64748b;">
                    <i class="fas fa-headset" style="margin-right: 5px;"></i> 
                    Issues with your data? <a href="#" style="color: #2563eb; text-decoration: none; font-weight: 600;">Contact IT Support</a>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection