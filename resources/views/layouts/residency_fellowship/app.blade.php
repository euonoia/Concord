<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Our Medical Legacy | Residency & Fellowship Recruitment</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="residency-recruitment">
    <nav class="navbar navbar-expand-lg sticky-top bg-white">
        <div class="container">
            <span class="navbar-brand fw-bold mb-0 h1">
                <span style="color: var(--primary-base)">CAREER</span> 
            </span>
            
        </div>
    </nav>

    @yield('content')

    <footer class="py-5 bg-dark text-white text-center" style="background-color: var(--neutral-900) !important;">
        <div class="container">
            <p class="mb-0">Concord Medical Center &copy; 2026</p>
        </div>
    </footer>
</body>
</html>