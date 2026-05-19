<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Cashier Dashboard') - {{ config('app.name') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts (Jost) -->
    <link
        href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    </link>  
    <!-- Custom Cashier Styles -->
    <link rel="stylesheet" href="{{ asset('css/cashier.css') }}">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Jost', sans-serif;
            color: #01693e;

        }
        .navbar-cashier {
            background: #cdeaf9;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-transform: capitalize;
            color: #01693e;
        }
        /* Ensure navbar text, brand, links and dropdowns use the desired color */
        .navbar-cashier .navbar-brand,
        .navbar-cashier .nav-link,
        .navbar-cashier .navbar-text,
        .navbar-cashier .dropdown-item,
        .navbar-cashier .navbar-toggler-icon,
        .navbar-cashier i {
            color: #01693e !important;
        }
        /* Light background on dropdown to keep contrast when needed */
        .navbar-cashier .dropdown-menu {
            background: #f8fffb;
        }
        .sidebar-cashier {
            background-color: #01693e;
            min-height: 100vh;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        }
        .sidebar-cashier .nav-link {
            color: #ecf0f1 !important;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .sidebar-cashier .nav-link:hover,
        .sidebar-cashier .nav-link.active {
            color: #fff !important;
            background-color: rgba(255,255,255,0.1);
            border-left-color: #667eea;
        }
        .main-content {
            padding: 30px;
        }
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card-header {
            border-radius: 8px 8px 0 0;
            font-weight: 600;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        .btn {
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #667eea;
            border-color: #667eea;
        }
        .btn-primary:hover {
            background-color: #5568d3;
            border-color: #5568d3;
        }

    </style>

    @yield('extra-css')
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-cashier navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand font-weight-bold" href="{{ route('cashier.dashboard', ['subdomain' => Auth::check() && Auth::user()->company ? Auth::user()->company->slug : (request()->route('subdomain') ?? (app()->has('company') ? app('company')->slug : null))]) }}">
                <i class="fas fa-cash-register mr-2" class="wow"></i>{{ auth()->user()->company->company_name }}'s Cashier
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse ml-auto" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                            <i class="fas fa-user-circle mr-2"></i>{{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('user.profile') }}">
                                <i class="fas fa-cog mr-2"></i>Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 sidebar-cashier">
                <ul class="nav flex-column pt-4">
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() === 'cashier.dashboard') active @endif" href="{{ cashierRoute('cashier.dashboard', ['subdomain' => Auth::check() && Auth::user()->company ? Auth::user()->company->slug : (request()->route('subdomain') ?? (app()->has('company') ? app('company')->slug : null))]) }}">
                            <i class="fas fa-home mr-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() === 'admin.pos') active @endif" href="{{ cashierRoute('cashier.pos', ['subdomain' => Auth::check() && Auth::user()->company ? Auth::user()->company->slug : (request()->route('subdomain') ?? (app()->has('company') ? app('company')->slug : null))]) }}">
                            <i class="fas fa-store mr-2"></i>New Sale (POS)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() === 'cashier.orders') active @endif" href="{{ cashierRoute('cashier.orders', ['subdomain' => Auth::check() && Auth::user()->company ? Auth::user()->company->slug : (request()->route('subdomain') ?? (app()->has('company') ? app('company')->slug : null))]) }}">
                            <i class="fas fa-shopping-bag mr-2"></i>Online Orders
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <a class="nav-link" href="{{ cashierRoute('cashier.shift-summary', ['subdomain' => Auth::check() && Auth::user()->company ? Auth::user()->company->slug : (request()->route('subdomain') ?? (app()->has('company') ? app('company')->slug : null))]) }}" onclick="loadShiftSummary(); return false;">
                            <i class="fas fa-chart-bar mr-2"></i>Shift Summary
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </li>
                </ul>

                <div class="mt-5 pt-4 border-top">
                    <small class="text-muted d-block p-3">
                        {{ auth()->user()->company->company_name }}<br>
                        <span class="badge badge-secondary">{{ auth()->user()->getRoleName() }}</span>
                    </small>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 main-content">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>

    <script>
        // Load shift summary via AJAX
        function loadShiftSummary() {
            fetch("{{ cashierRoute('cashier.shift-summary', ['subdomain' => Auth::check() && Auth::user()->company ? Auth::user()->company->slug : (request()->route('subdomain') ?? (app()->has('company') ? app('company')->slug : null))]) }}")
                .then(r => r.json())
                .then(data => {
                    alert(`Shift Summary:\n\nTotal Sales: Ksh ${data.total_sales}\nTransactions: ${data.sales_count}\nItems Sold: ${data.items_sold}`);
                })
                .catch(e => alert('Error loading shift summary'));
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


    @stack('scripts')
    @yield('extra-js')
</body>
</html>
