@extends('layouts.dashboard.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@php
    $user = Auth::user();
    $employeeRecord = \App\Models\Employee::where('user_id', $user->id)->first();
    
    $isClockedIn = false;
    
    if ($employeeRecord) {
        $isClockedIn = \App\Models\admin\Hr\hr3\AttendanceLog::where('employee_id', $employeeRecord->employee_id)
            ->whereNull('clock_out')
            ->exists();
    }
@endphp

{{-- 1. TOAST NOTIFICATION SECTION --}}
@if(session('error'))
    <div id="toast-container" style="position: fixed; top: 25px; right: 25px; z-index: 9999; animation: slideIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);">
        <div style="background: #ffffff; border-right: 4px solid #ef4444; padding: 1rem 1.5rem; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); display: flex; align-items: center; gap: 15px; min-width: 320px; border: 1px solid #fee2e2;">
            <div style="background: #fef2f2; color: #ef4444; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-clock-rotate-left" style="font-size: 1.2rem;"></i>
            </div>
            <div style="flex-grow: 1;">
                <h4 style="margin: 0; color: #1e293b; font-size: 0.95rem; font-weight: 700;">Shift Incomplete</h4>
                <p style="margin: 2px 0 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">
                    {{ session('error') }}
                </p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 5px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <style>
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
    </style>

    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-container');
            if(toast) {
                toast.style.animation = 'fadeOut 0.5s ease forwards';
                setTimeout(() => toast.remove(), 500);
            }
        }, 6000);
    </script>
@endif

@php
    $user = Auth::user();
    $employeeRecord = \App\Models\Employee::where('user_id', $user->id)->first();
    
    $isClockedIn = false;
    
    if ($employeeRecord) {
        // Use latest open record to match controller logic
        $isClockedIn = \App\Models\admin\Hr\hr3\AttendanceLog::where('employee_id', $employeeRecord->employee_id)
            ->whereNull('clock_out')
            ->exists();
    }
@endphp

<div class="dashboard-wrapper" style="padding: 2.5rem 1.5rem; max-width: 1300px; margin: 0 auto; font-family: 'Inter', sans-serif; background-color: #f8fafc;">
    
        <div class="header-section" style="flex-shrink: 0; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">        <div>
            <h1 style="color: #1a202c; font-size: 1.875rem; font-weight: 700; margin: 0; letter-spacing: -0.025em;">
                Welcome back, {{ $employee->first_name }}!
            </h1>
            <p style="color: #64748b; font-size: 1rem; margin-top: 0.5rem;">
                Overview of your attendance and schedule for today.
            </p>
         </div>

         <div style="display: flex; align-items: center; gap: 1rem;">
                @if($isClockedIn)
                    {{-- CLOCK OUT FORM --}}
                    <form action="{{ route('attendance.verify') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" 
                        title="Clock Out Now"
                        style="display: flex; align-items: center; justify-content: center; background: #dc2626; color: white; width: 48px; height: 48px; border-radius: 12px; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(220,38,38,0.2);"
                        onmouseover="this.style.backgroundColor='#b91c1c'; this.style.transform='translateY(-2px)'" 
                        onmouseout="this.style.backgroundColor='#dc2626'; this.style.transform='translateY(0)'">
                            <i class="fas fa-history" style="font-size: 1.25rem;"></i>
                        </button>
                    </form>
                @else
                    {{-- CLOCK IN LINK --}}
                    <a href="{{ route('user.attendance.scan') }}" 
                    title="Scan to Clock In"
                    style="display: flex; align-items: center; justify-content: center; background: #2563eb; color: white; width: 48px; height: 48px; border-radius: 12px; text-decoration: none; transition: all 0.2s; box-shadow: 0 4px 12px rgba(37,99,235,0.2);"
                    onmouseover="this.style.backgroundColor='#1d4ed8'; this.style.transform='translateY(-2px)'" 
                    onmouseout="this.style.backgroundColor='#2563eb'; this.style.transform='translateY(0)'">
                        <i class="fas fa-qrcode" style="font-size: 1.25rem;"></i>
                    </a>
                @endif


                <div class="date-chip" style="background: #ffffff; padding: 0.8rem 1.2rem; border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem; font-weight: 600; box-shadow: 0 1px 2px rgba(0,0,0,0.05); height: 48px; display: flex; align-items: center; gap: 12px;">
                    <div style="display: flex; align-items: center;">
                        <i class="far fa-calendar-alt" style="margin-right: 8px; color: #2563eb;"></i> 
                        {{ date('F j, Y') }}
                    </div>

                    <div style="width: 1px; height: 20px; background: #e2e8f0;"></div>

                    <div style="display: flex; align-items: center; color: #1e293b; min-width: 85px;">
                        <i class="far fa-clock" style="margin-right: 8px; color: #2563eb;"></i>
                        <span id="live-clock">--:--:--</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard_grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; align-content: start;">            <div class="dashboard_card" style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); overflow: hidden;">
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
                        
                        <div style="display: flex; flex-direction: column; align-items: flex-end; background: #eef2ff; padding: 6px 12px; border-radius: 6px; text-align: right;">
                            <span style="color: #6366f1; font-weight: 600; font-size: 0.9rem;">
                                {{ $employee->position->specialization_name ?? 'Unassigned' }}
                            </span>
                            
                            <span style="color: #4f46e5; font-weight: 500; font-size: 0.75rem; opacity: 0.8;">
                                {{ $employee->position->position_title ?? '' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            </div>
      <div class="quick-actions-container">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem;">
                <div style="color: #ea580c; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-calendar-alt" style="font-size: 1.1rem;"></i>
                </div>
                <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 600;">Weekly Shift</h3>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; max-height: 400px; overflow-y: auto; padding-right: 5px;">
                @forelse($allShifts as $shift)
                    @php
                        $isToday = $shift->day_of_week == now()->format('l');
                    @endphp
                    
                    <div style="background: {{ $isToday ? 'linear-gradient(135deg, #2B5F6B 0%, #1e424a 100%)' : '#ffffff' }}; 
                                color: {{ $isToday ? 'white' : '#1e293b' }}; 
                                padding: 1rem; border-radius: 12px; border: 1px solid #e2e8f0; 
                                display: flex; align-items: center; justify-content: space-between;
                                box-shadow: {{ $isToday ? '0 4px 12px rgba(43, 95, 107, 0.2)' : 'none' }};">
                        
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="background: {{ $isToday ? 'rgba(255,255,255,0.15)' : '#f1f5f9' }}; 
                                        width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                @if($shift->shift_name == 'Morning Shift')
                                    <i class="fas fa-sun" style="color: {{ $isToday ? '#fbbf24' : '#f59e0b' }};"></i>
                                @else
                                    <i class="fas fa-moon" style="color: {{ $isToday ? '#e0e7ff' : '#6366f1' }};"></i>
                                @endif
                            </div>
                            
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 700;">
                                    {{ $shift->day_of_week }} 
                                    @if($isToday) <span style="font-size: 0.65rem; background: #4ade80; color: #064e3b; padding: 2px 6px; border-radius: 4px; margin-left: 5px;">TODAY</span> @endif
                                </div>
                                <div style="font-size: 0.75rem; opacity: 0.8;">
                                    {{ $shift->shift_name }} • {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                        <p style="margin: 0; color: #64748b; font-size: 0.85rem;">No shifts assigned yet.</p>
                    </div>
                @endforelse
            </div>
                 </div>
                     </div>
                        </div>

    </div>
</div>
<script>
    function updateDashboardClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit', 
            hour12: true 
        });
        document.getElementById('live-clock').textContent = timeString;
    }

    // Run immediately and then every second
    updateDashboardClock();
    setInterval(updateDashboardClock, 1000);
</script>
@endsection
