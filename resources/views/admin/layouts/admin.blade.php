<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Admin | Control Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-width: 260px;
        }
        body { background-color: var(--bg); color: var(--text-dark); }
        .sidebar { 
            width: var(--sidebar-width); 
            height: 100vh; 
            position: fixed; 
            background: var(--primary-base); 
            color: white;
        }
        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: var(--space-xl); 
        }
        .nav-link { color: var(--neutral-300); padding: var(--space-md); border-radius: var(--radius-md); }
        .nav-link:hover, .nav-link.active { background: var(--primary-dark); color: white; }
    </style>
</head>
<body>
    <div class="sidebar d-flex flex-column p-3">
        <h4 class="mb-4 text-center">ADMIN PANEL</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="#" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            </li>
            <li>
                <a href="#" class="nav-link"><i class="bi bi-people me-2"></i> HR Management</a>
            </li>
            <li>
                <a href="#" class="nav-link"><i class="bi bi-file-earmark-text me-2"></i> Reports</a>
            </li>
        </ul>
        <hr>
        <form action="{{ route('portal.logout') }}" method="POST">
            @csrf
            <button class="btn btn-danger w-100">Logout</button>
        </form>
    </div>

    <div class="main-content">
        @yield('content')
    </div>
</body>
</html>