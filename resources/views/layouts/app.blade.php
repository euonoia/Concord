<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital System - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-slate-800 text-white p-4 flex justify-between items-center shadow-md">
        <div class="font-bold text-xl">HOSPITAL <span class="text-blue-400">OS</span></div>
        <div class="flex items-center gap-4">
            <span class="text-sm bg-slate-700 px-3 py-1 rounded-full text-slate-300">
                Logged in as: <strong>{{ Auth::user()->role_slug }}</strong>
            </span>
            <form action="{{ route('portal.logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm bg-red-600 hover:bg-red-700 px-3 py-1 rounded transition">Logout</button>
            </form>
        </div>
    </nav>

    <div class="p-8">
        @yield('content')
    </div>
</body>
</html>