<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCore HR1</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/hr/hr1/template.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: '#1B3C53',
              'primary-hover': '#234C6A',
              accent: '#456882',
              highlight: '#D2C1B6',
              bg: '#f7f8fa',
              'text-dark': '#1B3C53',
              'text-light': '#6b7280',
            },
            fontFamily: {
              sans: ['Inter', 'sans-serif'],
            },
          }
        }
      }
    </script>
    <style>
      body {
          background-color: #f7f8fa;
          color: #1B3C53;
          margin: 0;
          font-family: 'Inter', sans-serif;
      }
      * { box-sizing: border-box; transition: all 0.3s ease; }
      
      .main-inner {
          width: 100%;
          background: #fff;
          border-radius: 12px;
          padding: 25px;
          box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      }

      .card {
          background: #fff;
          border-radius: 12px;
          padding: 25px;
          text-align: center;
          box-shadow: 0 2px 6px rgba(0,0,0,0.08);
          border-top: 4px solid #456882;
          transition: all 0.2s ease;
      }

      .card:hover {
          transform: translateY(-5px);
          box-shadow: 0 4px 12px rgba(0,0,0,0.15);
          border-top-color: #234C6A;
      }

      ::-webkit-scrollbar {
          width: 6px;
      }
      ::-webkit-scrollbar-track {
          background: #f1f1f1;
      }
      ::-webkit-scrollbar-thumb {
          background: #1B3C53;
          border-radius: 10px;
      }
      
      [x-cloak] {
          display: none !important;
      }
    </style>
    @stack('styles')
</head>
<body class="bg-bg font-sans">
    @yield('content')
    @stack('scripts')
</body>
</html>

