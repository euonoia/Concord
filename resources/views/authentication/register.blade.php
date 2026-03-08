<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Portal - Unified Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md border-t-4 border-blue-600">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-slate-800">Create Account</h2>
            <p class="text-slate-500 mt-2">Access the Hospital Management System</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 text-sm">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('portal.register.submit') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Account Role & Department</label>
               <select id="role_slug" name="role_slug" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all" required>
    <option value="" disabled selected>Select your role</option>
    
    <optgroup label="HR Administration">
        <option value="admin_hr1">HR 1 (Admin 1)</option>
        <option value="admin_hr2">HR 2 (Admin 2)</option>
        <option value="admin_hr3">HR 3(Admin 3)</option>
        <option value="admin_hr4">HR 4(Admin 4)</option>
    </optgroup>

    <optgroup label="Logistics Administration">
        <option value="admin_logistics1">Logistics 1</option>
        <option value="admin_logistics2">Logistics 2</option>
    </optgroup>

    <optgroup label="Core Medical Admin">
        <option value="admin_core1">Core 1</option>
        <option value="admin_core2">Core 2</option>
    </optgroup>

    <optgroup label="Patient Portal">
        <option value="patient">Patient</option>
    </optgroup>
</select>
            </div>

            <div id="staff_fields" class="grid grid-cols-2 gap-4 hidden">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" 
                           class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" 
                           class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Username / ID Code</label>
                <input type="text" name="username" value="{{ old('username') }}" 
                       placeholder="Enter your assigned ID"
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                       placeholder="yourname@hospital.com"
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" 
                           class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Confirm</label>
                    <input type="password" name="password_confirmation" 
                           class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-md hover:shadow-lg transition duration-200">
                Register & Sign In
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-slate-600 text-sm">
                Already have an account? <a href="{{ route('portal.login') }}" class="text-blue-600 font-semibold hover:underline">Login here</a>
            </p>
        </div>
    </div>

    <script>
        const roleCategory = document.getElementById('role_category');
        const roleSlugHidden = document.getElementById('role_slug');
        const subRoleSelect = document.getElementById('sub_role_select');
        const staffFields = document.getElementById('staff_fields');
        const coreSubRoleFields = document.getElementById('core_sub_role_fields');

        function toggleFields() {
            const category = roleCategory.value;
            
            // Show names if role category contains 'employee' or 'admin'
            if (category.includes('employee') || category.includes('admin')) {
                staffFields.classList.remove('hidden');
            } else {
                staffFields.classList.add('hidden');
            }

            // Show/Hide Core Medical Sub-Roles
            if (category === 'core_employee') {
                coreSubRoleFields.classList.remove('hidden');
                roleSlugHidden.value = subRoleSelect.value;
            } else {
                coreSubRoleFields.classList.add('hidden');
                roleSlugHidden.value = category;
            }
        }

        roleCategory.addEventListener('change', toggleFields);
        subRoleSelect.addEventListener('change', toggleFields);
        window.addEventListener('DOMContentLoaded', toggleFields); 
    </script>
</body>
</html>