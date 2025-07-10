<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="d-flex">

    @include('layouts.partials.sidebar')

    <div class="main-content flex-grow-1 w-100">
        @include('layouts.partials.navbar')

        <main class="p-4">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
