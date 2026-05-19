@extends('layouts.softfyx')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-overlay"></div>

    <div class="hero-content">
        <h1 class="hero-title">Simplify. Fix. Elevate.</h1>
        <p class="hero-subtitle">
            Power your business with <span class="brand-highlight">Softifyx</span> —
            a smart, all-in-one platform for automation, payments, and growth.
        </p>

        <a href="{{ route('company.register') }}" class="hero-button">
            Register Your Company
        </a>
    </div>

    <div class="hero-decoration decoration-1"></div>
    <div class="hero-decoration decoration-2"></div>

</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <h2 class="section-title">
            Why Choose <span class="text-primary">Softifyx</span>?
        </h2>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <h3 class="feature-title">Smart Automation</h3>
                <p class="feature-description">Reduce manual work and streamline your operations with automated tools built for modern companies.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h3 class="feature-title">Integrated Payments</h3>
                <p class="feature-description">Seamlessly collect M-PESA and card payments through a secure, unified system.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="feature-title">Business Growth</h3>
                <p class="feature-description">Access insights and analytics that help you make smarter, faster decisions.</p>
            </div>
        </div>
    </div>
</section>

<!-- Subscription/Plans Section -->
<section class="plans-section" style="background: #fff; padding: 80px 0;">
    <div class="container">
        <h2 class="section-title" style="margin-bottom: 40px;">
            Choose Your <span class="text-primary">Plan</span>
        </h2>
        <div class="plans-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px;">
            <!-- Basic Plan -->
            <div class="plan-card" style="background: #f9fafb; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 40px 30px; text-align: center;">
                <h3 class="plan-title" style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 10px;">Basic</h3>
                <div class="plan-price" style="font-size: 2.5rem; font-weight: 700; color: #02ac05; margin-bottom: 10px;">Ksh 0 <span style="font-size: 1rem; color: #6b7280; font-weight: 400;">/ 30 days</span></div>
                <ul class="plan-features" style="list-style: none; padding: 0; margin-bottom: 24px; color: #374151;">
                    <li>✔ 1 Company</li>
                    <li>✔ 3 Users</li>
                    <li>✔ Core Features</li>
                    <li>✔ Email Support</li>
                </ul>
                <a href="{{ route('company.register') }}" class="hero-button" style="background: #02ac05; color: #fff;">Start Free Trial</a>
            </div>
            <!-- Pro Plan -->
            <div class="plan-card" style="background: #f9fafb; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 40px 30px; text-align: center; border: 2px solid #02ac05;">
                <h3 class="plan-title" style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 10px;">Pro</h3>
                <div class="plan-price" style="font-size: 2.5rem; font-weight: 700; color: #02ac05; margin-bottom: 10px;">Ksh 2,500 <span style="font-size: 1rem; color: #6b7280; font-weight: 400;">/ month</span></div>
                <ul class="plan-features" style="list-style: none; padding: 0; margin-bottom: 24px; color: #374151;">
                    <li>✔ 1 Company</li>
                    <li>✔ 10 Users</li>
                    <li>✔ All Basic Features</li>
                    <li>✔ Priority Support</li>
                    <li>✔ Advanced Analytics</li>
                </ul>
                <a href="{{ route('company.register') }}" class="hero-button" style="background: #02ac05; color: #fff;">Start Free Trial</a>
            </div>
            <!-- Enterprise Plan -->
            <div class="plan-card" style="background: #f9fafb; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 40px 30px; text-align: center;">
                <h3 class="plan-title" style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 10px;">Enterprise</h3>
                <div class="plan-price" style="font-size: 2.5rem; font-weight: 700; color: #02ac05; margin-bottom: 10px;">Ksh 7,500 <span style="font-size: 1rem; color: #6b7280; font-weight: 400;">/ month</span></div>
                <ul class="plan-features" style="list-style: none; padding: 0; margin-bottom: 24px; color: #374151;">
                    <li>✔ Unlimited Companies</li>
                    <li>✔ Unlimited Users</li>
                    <li>✔ All Pro Features</li>
                    <li>✔ Dedicated Support</li>
                    <li>✔ Custom Integrations</li>
                </ul>
                <a href="{{ route('company.register') }}" class="hero-button" style="background: #02ac05; color: #fff;">Start Free Trial</a>
            </div>
        </div>
    </div>
</section>

<style>
    .hero-section {
        position: relative;
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 50%, #14b8a6 100%);
        color: white;
        min-height: 85vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        overflow: hidden;
        font-family: 'Jost', sans-serif;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.2);
    }

    .hero-content {
        position: relative;
        z-index: 10;
        max-width: 800px;
        padding: 0 24px;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        line-height: 1.2;
        color: #000;
        animation: fadeInUp 0.8s ease-out;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        margin-bottom: 40px;
        opacity: 0.95;
        line-height: 1.6;
        animation: fadeInUp 0.8s ease-out 0.2s backwards;
    }

    .brand-highlight {
        font-weight: 600;
        color: #fbbf24;
    }

    .hero-button {
        display: inline-block;
        background: white;
        color: #02ac05;
        font-weight: 600;
        font-size: 1.05rem;
        padding: 14px 36px;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        text-decoration: none;
        transition: all 0.3s ease;
        animation: fadeInUp 0.8s ease-out 0.4s backwards;
    }

    .hero-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        background: #02ac05;
        color: #fff;
    }

    .hero-decoration {
        position: absolute;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        mix-blend-mode: overlay;
        opacity: 0.2;
        filter: blur(80px);
        animation: pulse 4s ease-in-out infinite;
    }

    .decoration-1 {
        top: -100px;
        left: -100px;
        background: #4338ca;
    }

    .decoration-2 {
        bottom: -100px;
        right: -100px;
        background: #2563eb;
        animation-delay: 1s;
    }

    /* Features Section */
    .features-section {
        padding: 80px 0;
        background: #f9fafb;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
    }

    .section-title {
        font-size: 2.25rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 60px;
        color: #1f2937;
    }

    .text-primary {
        color: #02ac05;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 40px;
    }

    .feature-card {
        background: white;
        border-radius: 16px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
        color: #02ac05;
        font-size: 3rem;
        margin-bottom: 20px;
    }

    .feature-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 12px;
        color: #1f2937;
    }

    .feature-description {
        font-size: 1rem;
        color: #6b7280;
        line-height: 1.6;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 0.2;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.3;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }

        .hero-subtitle {
            font-size: 1.1rem;
        }

        .hero-button {
            font-size: 1rem;
            padding: 12px 28px;
        }

        .section-title {
            font-size: 1.875rem;
        }

        .features-grid {
            gap: 30px;
        }

        .feature-card {
            padding: 30px 24px;
        }

        .feature-icon {
            font-size: 2.5rem;
        }

        .feature-title {
            font-size: 1.25rem;
        }
    }

    @media (max-width: 480px) {
        .hero-title {
            font-size: 2rem;
        }

        .hero-subtitle {
            font-size: 1rem;
        }

        .section-title {
            font-size: 1.5rem;
            margin-bottom: 40px;
        }
    }
</style>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endsection
