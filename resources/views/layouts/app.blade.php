<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hospital System')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    @stack('styles')

    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false,
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head_scripts')
</head>
<body class="@yield('body_class', 'bg-gray-100')" @yield('body_attrs')>
    @hasSection('app_nav')
        @yield('app_nav')
    @else
        @auth
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
        @endauth
    @endif

    <div class="min-h-screen">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>