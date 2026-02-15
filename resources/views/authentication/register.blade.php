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
                <label class="block text-sm font-medium text-slate-700 mb-1">Username / ID Code</label>
                <input type="text" name="username" value="{{ old('username') }}" 
                       placeholder="Enter your assigned ID or code"
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                       placeholder="yourname@hospital.com"
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Account Role & Department</label>
                <select name="role_slug" 
                        class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
                    <option value="" disabled selected>Select your role</option>
                    
                    <optgroup label="Patient Portal">
                        <option value="patient_standard" {{ old('role_slug') == 'patient_standard' ? 'selected' : '' }}>Standard Patient</option>
                        <option value="patient_guardian" {{ old('role_slug') == 'patient_guardian' ? 'selected' : '' }}>Patient Guardian</option>
                    </optgroup>

                    <optgroup label="Staff Departments">
                        <option value="hr_employee" {{ old('role_slug') == 'hr_employee' ? 'selected' : '' }}>Human Resources</option>
                        <option value="logistics_employee" {{ old('role_slug') == 'logistics_employee' ? 'selected' : '' }}>Logistics & Supply Chain</option>
                        <option value="finance_employee" {{ old('role_slug') == 'finance_employee' ? 'selected' : '' }}>Finance & Billing</option>
                        <option value="core_employee" {{ old('role_slug') == 'core_employee' ? 'selected' : '' }}>Core Medical Operations</option>
                    </optgroup>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" 
                           class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Confirm</label>
                    <input type="password" name="password_confirmation" 
                           class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition duration-200">
                Register & Sign In
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-slate-600 text-sm">
                Already have an account? <a href="{{ route('portal.login') }}" class="text-blue-600 font-semibold hover:underline">Login here</a>
            </p>
        </div>
    </div>

</body>
</html>