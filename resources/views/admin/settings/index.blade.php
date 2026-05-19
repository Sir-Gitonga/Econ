@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <!-- Header Section -->
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Company Settings</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ adminRoute('admin.dashboard') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Settings</div></li>
            </ul>
        </div>

        <!-- Alerts -->
        @if($errors->any())
            <div class="alert alert-danger mb-27">
                <h3 class="font-semibold mb-10">Please fix these errors:</h3>
                <ul class="list-disc list-inside space-y-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success mb-27">
                <i class="fas fa-check-circle"></i>
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Tab Navigation & Content Wrapper -->
        <div x-data="{ activeTab: 'general' }">

            <!-- Tab Navigation -->
            <div class="wg-box mb-27">
                <div class="settings-tabs-container">
                    @php
                        $tabs = [
                            'general' => ['icon' => 'fas fa-sliders-h', 'label' => 'General'],
                            'appearance' => ['icon' => 'fas fa-palette', 'label' => 'Appearance'],
                            'payments' => ['icon' => 'fas fa-credit-card', 'label' => 'Payments'],
                            'billing_subscription' => ['icon' => 'fas fa-file-invoice-dollar', 'label' => 'Billing & Subscription'],
                            'about' => ['icon' => 'fas fa-info-circle', 'label' => 'About'],
                            'sms' => ['icon' => 'fas fa-sms', 'label' => 'SMS'],
                            'whatsapp' => ['icon' => 'fas fa-comments', 'label' => 'WhatsApp'],
                            'business' => ['icon' => 'fas fa-chart-bar', 'label' => 'Business'],
                        ];
                    @endphp

                    @foreach($tabs as $key => $tab)
                        <button
                            @click="activeTab = '{{ $key }}'"
                            :class="activeTab === '{{ $key }}' ? 'active-tab' : 'inactive-tab'"
                            class="settings-tab-button">
                            <i class="{{ $tab['icon'] }}"></i>
                            <span>{{ $tab['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Tab Content Sections -->
            <div class="wg-box">

            <!-- GENERAL SETTINGS -->
            <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-200">
                @include('admin.settings.partials.general', ['companySetting' => $companySetting])
            </div>

            <!-- APPEARANCE SETTINGS -->
            <div x-show="activeTab === 'appearance'" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-200">
                @include('admin.settings.partials.appearance', ['appearanceSetting' => $appearanceSetting])
            </div>

            <!-- PAYMENTS SETTINGS -->
            <div x-show="activeTab === 'payments'" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-200">
                @include('admin.settings.partials.payments', ['paymentSetting' => $paymentSetting])
            </div>


            <!-- BILLING & SUBSCRIPTION SETTINGS -->
            <div x-show="activeTab === 'billing_subscription'" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-200">
                @include('admin.settings.partials.billing-subscription', [
                    'isInTrial' => $isInTrial,
                    'remainingTrialDays' => $remainingTrialDays,
                    'shouldShowPaymentDue' => $shouldShowPaymentDue,
                    'company' => $company,
                    'currentPlan' => $currentPlan,
                    'daysUntilExpiry' => $daysUntilExpiry,
                    'isPaymentAvailable' => $isPaymentAvailable,
                    'plans' => $plans,
                    'paymentHistory' => $paymentHistory
                ])
            </div>

            <!-- ABOUT SETTINGS -->
            <div x-show="activeTab === 'about'" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-200">
                @include('admin.settings.partials.about', ['businessSetting' => $businessSetting])
            </div>

            <!-- SMS SETTINGS -->
            <div x-show="activeTab === 'sms'" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-200">
                @include('admin.settings.partials.sms', ['smsSetting' => $company->smsSetting ?? null])
            </div>

            <!-- WHATSAPP SETTINGS -->
            <div x-show="activeTab === 'whatsapp'" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-200">
                @include('admin.settings.partials.whatsapp', ['whatsappSetting' => $company->whatsappSetting ?? null])
            </div>

            <!-- BUSINESS SETTINGS -->
            <div x-show="activeTab === 'business'" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-200">
                @include('admin.settings.partials.business', ['businessSetting' => $businessSetting])
            </div>

        </div>

    </div>
</div>

<style>
    .active-tab {
        background-color: #f5f5f5;
        border-bottom: 3px solid #4F46E5;
        color: #4F46E5;
        font-size: 16px;
    }

    .inactive-tab {
        border-bottom: 2px solid #e5e5e5;
        color: #666;
        font-size: 16px;
    }

    .inactive-tab:hover {
        background-color: #f9f9f9;
    }

    .preview-image {
        height: 80px;
        width: 80px;
        border-radius: 8px;
        object-fit: cover;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f5f5f5;
    }

    .preview-image i {
        font-size: 32px;
        color: #ccc;
    }

    .preview-color {
        height: 80px;
        width: 80px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        border: 4px solid white;
    }

    .input-color {
        height: 48px;
        width: 80px;
        border: 2px solid #e5e5e5;
        border-radius: 8px;
        cursor: pointer;
    }

    .input-color:hover {
        border-color: #4F46E5;
    }

    .upload-image {
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 24px;
        background: linear-gradient(135deg, #f9f9f9 0%, #ffffff 100%);
        border: 2px dashed #e5e5e5;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .upload-image:hover {
        border-color: #4F46E5;
        background-color: rgba(79, 70, 229, 0.04);
    }

    .upload-input {
        flex: 1;
    }

    .upload-input label {
        cursor: pointer;
    }

    .upload-input .link {
        color: #4F46E5;
        font-weight: 600;
        text-decoration: none;
    }

    .upload-input .link:hover {
        text-decoration: underline;
    }
</style>

<!-- Alpine.js for section switching -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

@endsection
