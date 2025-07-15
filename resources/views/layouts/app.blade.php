<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />

    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
</head>
<body class="bg-light">

    {{-- Sidebar --}}
    @include('layouts.partials.sidebar')

    {{-- Main Content --}}
    <div class="main-content" id="mainContent">
        {{-- Navbar --}}
        @include('layouts.partials.navbar')

        {{-- Scrollable Content --}}
        <main class="p-2" style="margin-top: 60px; height: calc(100vh - 60px); overflow-y: auto;">
            @yield('content')
        </main>
    </div>

    {{-- Responsive margin --}}
    <style>
        .main-content {
            transition: margin-left 0.3s ease;
        }
        
        @media (min-width: 992px) {
            .main-content {
                margin-left: 220px;
            }
        }
    </style>

    @stack('scripts')
</body>
</html>