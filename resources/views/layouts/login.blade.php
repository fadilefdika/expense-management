<!-- resources/views/layouts/login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-light">

    <main class="d-flex align-items-center justify-content-center min-vh-100">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
