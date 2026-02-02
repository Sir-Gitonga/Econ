{{-- resources/views/layouts/company.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Company Portal' }} | Softifyx</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <main class="flex-grow flex items-center justify-center p-4">
        @yield('content')
    </main>

    <footer class="text-center text-gray-500 text-sm py-4">
        &copy; {{ date('Y') }} Softifyx — Simplify • Fix • Elevate
    </footer>

    @stack('scripts')
</body>
</html>
