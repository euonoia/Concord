@extends('layouts.dashboard.app')

@section('content')
<div class="dashboard_container" style="padding: 2rem; max-width: 1200px; margin: 0 auto;">
    <div class="header-section" style="margin-bottom: 2rem;">
        <p style="color: #718096; font-size: 1rem;">Here’s your HR2 summary overview and quick access to your development tools.</p>
    </div>

    <div class="dashboard_grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem;">
        
        <div class="dashboard_card" style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 1.5rem;">
            <div style="display: flex; align-items: center; margin-bottom: 1.25rem; border-bottom: 2px solid #edf2f7; padding-bottom: 0.75rem;">
                <i class="fas fa-user-circle" style="color: #4a5568; font-size: 1.25rem; margin-right: 10px;"></i>
                <h3 style="margin: 0; font-size: 1.15rem; color: #1a202c;">Profile Details</h3>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f7fafc;">
                    <span style="color: #718096;"><i class="fas fa-id-badge" style="width: 20px;"></i> Employee ID</span>
                    <strong style="color: #2d3748;">{{ $employee->employee_id ?? 'Pending' }}</strong>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f7fafc;">
                    <span style="color: #718096;"><i class="fas fa-font" style="width: 20px;"></i> Full Name</span>
                    <strong style="color: #2d3748;">{{ $employee->first_name }} {{ $employee->last_name }}</strong>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f7fafc;">
                    <span style="color: #718096;"><i class="fas fa-building" style="width: 20px;"></i> Department</span>
                    <strong style="color: #2d3748;">{{ $employee->department ?? 'General' }}</strong>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
                    <span style="color: #718096;"><i class="fas fa-star" style="width: 20px;"></i> Specialization</span>
                    <strong style="color: #2d3748;">{{ $employee->specialization ?? 'N/A' }}</strong>
                </li>
            </ul>
        </div>

        <div class="dashboard_card" style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 1.5rem;">
            <div style="display: flex; align-items: center; margin-bottom: 1.25rem; border-bottom: 2px solid #edf2f7; padding-bottom: 0.75rem;">
                <i class="fas fa-bolt" style="color: #ed8936; font-size: 1.25rem; margin-right: 10px;"></i>
                <h3 style="margin: 0; font-size: 1.15rem; color: #1a202c;">Quick Actions</h3>
            </div>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="{{ route('user.training.index') }}" class="btn" style="background-color: #3182ce; color: white; padding: 0.75rem; border-radius: 8px; text-decoration: none; text-align: center; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i class="fas fa-chalkboard-teacher"></i> Browse Trainings
                </a>
                <a href="{{ route('user.learning.index') }}" class="btn" style="background-color: #805ad5; color: white; padding: 0.75rem; border-radius: 8px; text-decoration: none; text-align: center; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i class="fas fa-book-open"></i> My Learning Modules
                </a>
            </div>
            <p style="margin-top: 1.5rem; font-size: 0.85rem; color: #a0aec0; text-align: center;">
                <i class="fas fa-info-circle"></i> Need help? Contact HR Support.
            </p>
        </div>

    </div>
</div>
@endsection