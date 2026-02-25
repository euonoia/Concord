@extends('admin.layouts.app')

@section('title', 'Succession Planning - HR2 Admin')

@section('content')
<div class="container" style="padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <h2 style="font-weight: 700; color: #333; margin-bottom: 25px;">
        <i class="fas fa-seedling" style="color: #28a745; margin-right: 10px;"></i>Succession Planning
    </h2>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 8px; border-left: 5px solid #28a745;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Include Positions Section --}}
    @include('admin.hr2.positions')

    {{-- Include Candidates Section --}}
    @include('admin.hr2.candidates')
</div>

{{-- JavaScript for Dynamic Specialization --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deptSelect = document.getElementById('dept_select');
    const specSelect = document.getElementById('spec_select');

    deptSelect.addEventListener('change', function() {
        const deptCode = this.value;
        specSelect.innerHTML = '<option value="">Loading...</option>';

        if (!deptCode) {
            specSelect.innerHTML = '<option value="">-- Choose Dept First --</option>';
            return;
        }

        fetch(`/admin/hr2/departments/${deptCode}/specializations`)
            .then(response => response.json())
            .then(data => {
                specSelect.innerHTML = '<option value="">-- Select Specialization --</option>';
                if(data.length === 0) {
                    specSelect.innerHTML = '<option value="">No specializations available</option>';
                } else {
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.specialization_name;
                        opt.textContent = item.specialization_name;
                        specSelect.appendChild(opt);
                    });
                }
            })
            .catch(err => {
                console.error(err);
                specSelect.innerHTML = '<option value="">Error loading data</option>';
            });
    });
});
</script>
@endsection