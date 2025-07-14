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

</head>
<body class="bg-light" style="min-height: 100vh; overflow: hidden;">

    {{-- Sidebar --}}
    <div id="sidebar" class="position-fixed top-0 start-0 vh-100 bg-dark text-white d-none d-md-block"
         style="width: 220px; z-index: 1030;">
        @include('layouts.partials.sidebar')
    </div>

    {{-- Main Content --}}
    <div class="main-content" id="mainContent">
        {{-- Navbar --}}
        <div id="navbar" class="position-fixed top-0 start-0 end-0 bg-white" style="height: 60px; z-index: 1020;">
            @include('layouts.partials.navbar')
        </div>

        {{-- Scrollable Content --}}
        <main class="p-4" style="margin-top: 60px; height: calc(100vh - 60px); overflow-y: auto;">
            @yield('content')
        </main>
    </div>

    {{-- Responsive margin --}}
    <style>
        @media (min-width: 768px) {
            #mainContent {
                margin-left: 220px;
            }
            #navbar {
                margin-left: 220px;
            }
        }
    </style>
@stack('scripts')
</body>


</html>
