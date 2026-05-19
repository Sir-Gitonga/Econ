<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin Panel') - {{ config('app.name') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SweetAlert2 CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.0/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.0/dist/sweetalert2.min.js"></script>

    <!-- Additional styles -->
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-4">
                <h1 class="text-xl font-bold">Super Admin</h1>
            </div>
            <nav class="mt-8">
                <a href="{{ route('superadmin.dashboard') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('superadmin.dashboard') ? 'bg-gray-700' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('superadmin.tenants.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('superadmin.tenants.*') ? 'bg-gray-700' : '' }}">
                    Tenants
                </a>
                <a href="{{ route('superadmin.subscriptions.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('superadmin.subscriptions.*') ? 'bg-gray-700' : '' }}">
                    Subscriptions
                </a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex-1">
            <!-- Top navbar -->
            <header class="bg-white shadow-sm border-b">
                <div class="px-4 py-3 flex justify-between items-center">
                    <h2 class="text-lg font-semibold">@yield('page-title')</h2>
                    <div class="flex items-center space-x-4">
                        <span>Welcome, {{ auth('superadmin')->user()->name }}</span>
                        <form method="POST" action="{{ route('superadmin.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')

    <!-- Modal Confirmation Functions -->
    <script>
        // Function to show SweetAlert2 confirmation modal
        function confirmAction(message, actionType = 'default') {
            const config = {
                default: {
                    icon: 'warning',
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'Yes, proceed',
                    cancelButtonText: 'Cancel'
                },
                suspend: {
                    icon: 'warning',
                    title: 'Suspend Tenant?',
                    html: message || 'Are you sure you want to suspend this tenant? Users will not be able to access it.',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Yes, suspend',
                    cancelButtonText: 'Cancel'
                },
                activate: {
                    icon: 'success',
                    title: 'Activate Tenant?',
                    html: message || 'Are you sure you want to activate this tenant?',
                    confirmButtonColor: '#16a34a',
                    confirmButtonText: 'Yes, activate',
                    cancelButtonText: 'Cancel'
                },
                delete: {
                    icon: 'error',
                    title: 'Delete Permanently?',
                    html: message || 'This action cannot be undone. Are you sure?',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel'
                }
            };

            const settings = config[actionType] || config.default;

            return Swal.fire({
                ...settings,
                allowOutsideClick: false,
                showCancelButton: true,
                cancelButtonColor: '#6b7280'
            }).then((result) => result.isConfirmed);
        }

        // Override form submit handler for confirmation modals
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(e) {
                if (e.target.closest('button[data-confirm]')) {
                    e.preventDefault();
                    const button = e.target.closest('button[data-confirm]');
                    const message = button.getAttribute('data-message') || 'Are you sure?';
                    const actionType = button.getAttribute('data-confirm');
                    const form = button.closest('form');

                    confirmAction(message, actionType).then((confirmed) => {
                        if (confirmed && form) {
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>