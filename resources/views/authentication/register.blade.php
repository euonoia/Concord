<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Portal - Patient Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md border-t-4 border-blue-600">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-slate-800">Create Account</h2>
            <p class="text-slate-500 mt-2">Join the Hospital Patient Portal</p>
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
                <label class="block text-sm font-medium text-slate-700 mb-1">Patient Code / Username</label>
                <input type="text" name="username" value="{{ old('username') }}" 
                       placeholder="e.g. PAT-12345"
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
                <p class="text-xs text-slate-500 mt-1">Use the code provided during your hospital visit.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                       placeholder="yourname@example.com"
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
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
                Register Account
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