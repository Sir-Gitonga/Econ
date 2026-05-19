{{-- Master layout for user dashboard pages --}}
@extends('layouts.app')

@push('styles')
<style>
:root {
    --primary: #6366f1;
    --primary-light: #eef2ff;
    --primary-dark: #4f46e5;
    --secondary: #8b5cf6;
    --success: #10b981;
    --success-light: #d1fae5;
    --danger: #ef4444;
    --danger-light: #fee2e2;
    --warning: #f59e0b;
    --warning-light: #fef3c7;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-900: #111827;
    --white: #ffffff;
    --black: #000000;
    --radius-sm: 6px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Basic Dashboard Styles */
.user-dashboard-wrapper {
    padding: 2rem 0;
    background-color: #f8fafc;
    margin-top: 60px;
}

.user-dashboard-container {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.user-sidebar {
    background: white;
    border-radius: 12px;
    padding: 1.5rem 0;
    height: fit-content;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.user-sidebar-nav {
    list-style: none;
    margin: 0;
    padding: 0;
}

.user-sidebar-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    color: #6b7280;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
}

.user-sidebar-link:hover {
    color: #6366f1;
    background-color: #eef2ff;
}

.user-sidebar-link.active {
    color: #6366f1;
    background-color: #eef2ff;
    font-weight: 600;
}

.user-sidebar-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #6366f1;
    border-radius: 0 2px 2px 0;
}

.user-sidebar-link i {
    width: 20px;
    text-align: center;
}

.user-sidebar-logout {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.user-content {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.page-header {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.page-header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #6b7280;
    font-size: 0.95rem;
}

/* Card Styles */
.card-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.dashboard-card {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.dashboard-card.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.dashboard-card.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.dashboard-card.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

.dashboard-card-label {
    font-size: 0.85rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.dashboard-card-value {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.dashboard-card-subtitle {
    font-size: 0.85rem;
    opacity: 0.85;
}

/* Order Cards */
.order-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.order-card:hover {
    border-color: #6366f1;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

/* Badge Styles */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.badge-success { background-color: #d1fae5; color: #047857; }
.badge-danger { background-color: #fee2e2; color: #b91c1c; }
.badge-warning { background-color: #fef3c7; color: #b45309; }
.badge-ordered { background-color: #dbeafe; color: #0c4a6e; }

/* Button Styles */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-secondary {
    background-color: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background-color: #d1d5db;
}

/* Form Styles */
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #374151;
    font-weight: 600;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
}

.empty-state-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    color: #111827;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #6b7280;
    margin-bottom: 1.5rem;
}

/* Responsive */
@media (max-width: 1024px) {
    .user-dashboard-container {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .user-content {
        padding: 1rem;
    }
    
    .card-container {
        grid-template-columns: 1fr;
    }
    
    .page-header h1 {
        font-size: 1.5rem;
    }
}
    .user-sidebar-nav li {
        border-bottom: 1px solid #f3f4f6;
    }

    .user-sidebar-nav li:last-child {
        border-bottom: none;
    }

    .user-sidebar-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        color: #374151;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .user-sidebar-link:hover {
        color: #6366f1;
        background-color: #eef2ff;
    }

    .user-sidebar-link.active {
        color: #6366f1;
        background-color: #eef2ff;
        font-weight: 600;
    }

    .user-sidebar-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #6366f1;
        border-radius: 0 2px 2px 0;
    }

    .user-sidebar-link i {
        width: 20px;
        text-align: center;
    }

    .user-sidebar-logout {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #f3f4f6;
    }

    /* ============================================
       MAIN CONTENT AREA
       ============================================ */
    .user-content {
        background: #ffffff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* ============================================
       PAGE HEADER
       ============================================ */
    .page-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #f3f4f6;
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .page-header p {
        color: #6b7280;
        font-size: 0.95rem;
    }

    /* ============================================
       CARDS & SECTIONS
       ============================================ */
    .card-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .dashboard-card {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: #ffffff;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .dashboard-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .dashboard-card-content {
        position: relative;
        z-index: 1;
    }

    .dashboard-card-label {
        font-size: 0.85rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .dashboard-card-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .dashboard-card-subtitle {
        font-size: 0.85rem;
        opacity: 0.85;
    }

    .dashboard-card.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .dashboard-card.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .dashboard-card.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    /* ============================================
       ORDER & ITEM CARDS
       ============================================ */
    .order-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .order-card:hover {
        border-color: #6366f1;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .order-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .order-card-id {
        font-weight: 600;
        color: #111827;
        font-size: 1rem;
    }

    .order-card-date {
        color: #6b7280;
        font-size: 0.85rem;
    }

    .order-card-body {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1rem;
    }

    .order-card-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .order-card-label {
        color: #6b7280;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .order-card-value {
        color: #111827;
        font-weight: 600;
        font-size: 1rem;
    }

    .order-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid #f3f4f6;
    }

    .order-card-actions {
        display: flex;
        gap: 0.5rem;
    }

    /* ============================================
       BADGES
       ============================================ */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .badge-success {
        background-color: #d1fae5;
        color: #047857;
    }

    .badge-danger {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .badge-warning {
        background-color: #fef3c7;
        color: #b45309;
    }

    .badge-ordered {
        background-color: #dbeafe;
        color: #0c4a6e;
    }

    /* ============================================
       BUTTONS
       ============================================ */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.2rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #ffffff;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .btn-secondary {
        background-color: #e5e7eb;
        color: #111827;
    }

    .btn-secondary:hover {
        background-color: #d1d5db;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
    }

    .btn-icon {
        width: 40px;
        height: 40px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    /* ============================================
       FORMS
       ============================================ */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #374151;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* ============================================
       EMPTY STATE
       ============================================ */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state h3 {
        color: #111827;
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
    }

    .empty-state p {
        color: #6b7280;
        margin-bottom: 1.5rem;
    }

    /* ============================================
       TABLE STYLING
       ============================================ */
    .responsive-table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
    }

    .responsive-table thead {
        background-color: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    .responsive-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #374151;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .responsive-table td {
        padding: 1rem;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
    }

    .responsive-table tbody tr:hover {
        background-color: #f9fafb;
    }

    /* ============================================
       PRODUCT GRID
       ============================================ */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .product-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-card:hover {
        border-color: #6366f1;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transform: translateY(-4px);
    }

    .product-image {
        width: 100%;
        height: 200px;
        background: #f3f4f6;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-content {
        padding: 1rem;
    }

    .product-name {
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .product-price {
        color: #6366f1;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 0.75rem;
    }

    /* ============================================
       ANIMATIONS
       ============================================ */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .card-container > * {
        animation: slideIn 0.5s ease-out;
    }

    /* ============================================
       RESPONSIVE DESIGN
       ============================================ */
    @media (max-width: 768px) {
        .user-content {
            padding: 1rem;
        }

        .card-container {
            grid-template-columns: 1fr;
        }

        .order-card-body {
            grid-template-columns: repeat(2, 1fr);
        }

        .page-header h1 {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .user-dashboard-container {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .page-header {
            margin-bottom: 1rem;
        }

        .user-sidebar {
            padding: 1rem 0;
        }

        .user-sidebar-link {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
    }

    /* ============================================
       UTILITY CLASSES
       ============================================ */
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-muted { color: #6b7280; }
    .text-primary { color: #6366f1; }
    .text-danger { color: #ef4444; }
    .text-success { color: #10b981; }
    
    .mt-1 { margin-top: 0.5rem; }
    .mt-2 { margin-top: 1rem; }
    .mt-3 { margin-top: 1.5rem; }
    .mb-1 { margin-bottom: 0.5rem; }
    .mb-2 { margin-bottom: 1rem; }
    .mb-3 { margin-bottom: 1.5rem; }
    
    .flex { display: flex; }
    .flex-between { display: flex; justify-content: space-between; align-items: center; }
    .flex-center { display: flex; justify-content: center; align-items: center; }
    .gap-1 { gap: 0.5rem; }
    .gap-2 { gap: 1rem; }
</style>
@endpush

{{-- INTERACTION SCRIPTS --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling to anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading states to buttons
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function() {
            if (this.form || this.hasAttribute('onclick')) {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Loading...</span>';
                this.disabled = true;
                
                // Reset after 2 seconds if no form submission
                setTimeout(() => {
                    if (!this.form || !this.form.submitted) {
                        this.innerHTML = originalHTML;
                        this.disabled = false;
                    }
                }, 2000);
            }
        });
    });

    // Add hover effects for cards
    document.querySelectorAll('.order-card, .product-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
});
</script>

@section('content')
<div class="user-dashboard-wrapper">
    <div class="container">
        <div class="user-dashboard-container">
            {{-- SIDEBAR NAVIGATION --}}
            <aside class="user-sidebar">
                <nav>
                    <ul class="user-sidebar-nav">
                        <li>
                            <a href="{{ route('user.dashboard') }}" class="user-sidebar-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-home"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.orders') }}" class="user-sidebar-link {{ request()->routeIs('user.orders') ? 'active' : '' }}">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Orders</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.addresses') }}" class="user-sidebar-link {{ request()->routeIs('user.addresses') ? 'active' : '' }}">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Addresses</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.account') }}" class="user-sidebar-link {{ request()->routeIs('user.account') ? 'active' : '' }}">
                                <i class="fas fa-user-cog"></i>
                                <span>Account</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.wishlist') }}" class="user-sidebar-link {{ request()->routeIs('user.wishlist') ? 'active' : '' }}">
                                <i class="fas fa-heart"></i>
                                <span>Wishlist</span>
                            </a>
                        </li>
                        <li class="user-sidebar-logout">
                            <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
                                @csrf
                            </form>
                            <a href="#" class="user-sidebar-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            {{-- MAIN CONTENT --}}
            <main class="user-content">
                @yield('dashboard-content')
            </main>
        </div>
    </div>
</div>
@endsection
