<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Softifyx | Simplify. Fix. Elevate.')</title>
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ========== RESET ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

       body {
            font-family: 'Jost', sans-serif;
            color: #1e1b4b;
            background-color: #ffffff;
        }


        /* ========== NAVBAR ========== */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 6%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #eee;
            z-index: 100;
            transition: box-shadow 0.3s;
        }

        nav.scrolled {
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
        }

        /* Brand/Logo */
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .brand img {
            width: 100px;
            height: auto;
            max-height: 50px;
            object-fit: contain;
        }

        .brand span {
            font-size: 1.2rem;
            font-weight: 700;
            color: #6366f1;
        }

        /* Desktop Navigation Links */
        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: #6b7280;
            font-size: 0.88rem;
            font-weight: 500;
            transition: color 0.25s;
        }

        .nav-links a:hover {
            color: #6366f1;
        }

        /* Action Buttons (Login/Register) */
        .nav-actions {
            display: flex;
            gap: 1rem;
        }

        .nav-actions a {
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.25s;
        }

        .nav-actions .login {
            color: #02ac05;
            border: 2px solid #02ac05;
        }

        .nav-actions .login:hover {
            background: #02ac05;
            color: #fff;
        }

        .nav-actions .register {
            color: #fff;
            background: #6366f1;
            border-radius: 99px;
        }

        .nav-actions .register:hover {
            background: #02ac05;
        }

        /* Mobile Menu Toggle (Hamburger) */
        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            border: none;
            background: none;
        }

        .menu-toggle span {
            width: 25px;
            height: 2px;
            background: #1e1b4b;
            transition: 0.3s;
        }

        /* Mobile Dropdown Menu */
        .mobile-menu {
            position: fixed;
            top: 68px;
            left: 0;
            width: 100%;
            background: #fff;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s;
            z-index: 99;
        }

        .mobile-menu.active {
            max-height: 400px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.356);
        }

        .mobile-menu a {
            display: block;
            padding: 15px 6%;
            color: #000000;
            text-decoration: none;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s;
        }

        .mobile-menu a:hover {
            background: #02ac0560;
            color: #6366f1;
        }

        /* ========== MAIN CONTENT ========== */
        main {
            padding-top: 68px;
            min-height: calc(100vh - 68px);
        }

        /* ========== FOOTER ========== */
        footer {
            background: #101F3C;
            color: #021331;
            padding: 4rem 6% 2rem;
            margin-top: 6rem;
        }

        .footer-grid {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.8fr 1fr 1fr;
            gap: 3rem;
        }

        /* Footer Brand Section */
        .footer-brand span {
            display: block;
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.6rem;
        }

        .footer-brand p {
            font-size: 0.82rem;
            line-height: 1.7;
            color: #6b7280;
            max-width: 240px;
        }

        /* Footer Columns */
        .footer-col h4 {
            color: #fff;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            margin-bottom: 1.1rem;
        }

        .footer-col a {
            display: block;
            text-decoration: none;
            color: #6b7280;
            font-size: 0.82rem;
            margin-bottom: 0.55rem;
            transition: color 0.2s;
        }

        .footer-col a:hover {
            color: #a5b4fc;
        }

        /* Footer Bottom (Copyright) */
        .footer-bottom {
            max-width: 1000px;
            margin: 2.5rem auto 0;
            padding-top: 1.8rem;
            border-top: 1px solid #1f2937;
            text-align: center;
            font-size: 0.75rem;
            color: #4b5563;
        }

        /* ========== RESPONSIVE DESIGN ========== */

        /* Tablets (768px and below) */
        @media (max-width: 768px) {
            .brand img {
                width: 80px;
                max-height: 40px;
            }

            .nav-links,
            .nav-actions {
                display: none;
            }

            .menu-toggle {
                display: flex;
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }
        }

        /* Mobile (480px and below) */
        @media (max-width: 480px) {
            nav {
                height: 60px;
            }

            main {
                padding-top: 60px;
            }

            .mobile-menu {
                top: 60px;
            }

            .brand img {
                width: 60px;
                max-height: 35px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .footer-brand p {
                max-width: 100%;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- ========== NAVBAR ========== -->
    <nav id="navbar">
        <a href="{{ url('/') }}" class="brand">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Softifyx">
            <span>Softifyx</span>
        </a>

        <div class="nav-links">
            <a href="{{ url('/') }}">Home</a>
            <a href="#features">Features</a>
            <a href="#pricing">Pricing</a>
            <a href="#contact">Contact</a>
        </div>

        <div class="nav-actions">
            <a href="{{ route('login') }}" class="login">Login</a>
            <a href="{{ route('company.register') }}" class="register">Get Started - Free</a>
        </div>

        <button class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    <!-- ========== MOBILE MENU ========== -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="{{ url('/') }}">Home</a>
        <a href="#features">Features</a>
        <a href="#pricing">Pricing</a>
        <a href="#contact">Contact</a>
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('company.register') }}">Get Started</a>
    </div>

    <!-- ========== MAIN CONTENT ========== -->
    <main>
        @yield('content')
    </main>

    <!-- ========== FOOTER ========== -->
    <footer>
        <div class="footer-grid">
            <div class="footer-brand">
                <span>Softifyx</span>
                <p>Simplify. Fix. Elevate.<br>Tools built to help your business scale effortlessly.</p>
            </div>

            <div class="footer-col">
                <h4>Company</h4>
                <a href="#">About Us</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact</a>
            </div>

            <div class="footer-col">
                <h4>Support</h4>
                <a href="#">Help Center</a>
                <a href="mailto:support@softifyx.com">support@softifyx.com</a>
                <a href="tel:+2547110848885">+254 110848885</a>
            </div>
        </div>

        <div class="footer-bottom">
            © {{ date('Y') }} Softifyx. All rights reserved.
        </div>
    </footer>

    <!-- ========== SCRIPTS ========== -->
    <script>
        // Get elements
        const navbar = document.getElementById('navbar');
        const menuToggle = document.getElementById('menuToggle');
        const mobileMenu = document.getElementById('mobileMenu');

        // Add shadow to navbar on scroll
        window.addEventListener('scroll', function() {
            if (window.scrollY > 30) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Toggle mobile menu
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
        });

        // Close mobile menu when clicking a link
        const menuLinks = mobileMenu.querySelectorAll('a');
        menuLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
