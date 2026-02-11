<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Portal - Staff Onboarding</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-6">

    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-2xl">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-slate-800">Staff Registration</h2>
            <p class="text-slate-500">Create a new internal employee record</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('staff.register.submit') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Employee ID / Code</label>
                    <input type="text" name="employee_code" value="{{ old('employee_code') }}" 
                           placeholder="e.g., DOC-2026-001"
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">First Name</label>
                    <input type="text" name="firstname" value="{{ old('firstname') }}" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Last Name</label>
                    <input type="text" name="lastname" value="{{ old('lastname') }}" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Department ID</label>
                    <input type="number" name="department_id" value="{{ old('department_id') }}" 
                           placeholder="e.g., 10"
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Specialization</label>
                    <input type="text" name="specialization" value="{{ old('specialization') }}" 
                           placeholder="e.g., Cardiology"
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">System Password</label>
                    <input type="password" name="password" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" 
                        class="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition duration-200 shadow-lg">
                    Register Employee
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('staff.login') }}" class="text-sm text-indigo-600 hover:underline">Return to Staff Login</a>
        </div>
    </div>

</body>
</html>