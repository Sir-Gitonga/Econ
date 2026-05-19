
<!-- Billing & Subscription Partial: expects variables passed from parent view -->
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900">Billing & Subscription</h1>
            <p class="mt-2 text-gray-600">Manage your subscription plan and payments</p>
        </div>

        <!-- Trial Status Card -->
        @if(isset($isInTrial) && $isInTrial)
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-600 p-6 rounded">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-blue-900">Free Trial Active</h3>
                    <p class="mt-1 text-blue-800">You have <strong>{{ $remainingTrialDays ?? '?' }} days</strong> remaining on your 30-day free trial. No payment required until {{ $company->trial_ends_at->format('M d, Y') ?? '' }}.</p>
                    @if(isset($shouldShowPaymentDue) && $shouldShowPaymentDue)
                    <p class="mt-2 text-yellow-800 font-semibold">⚠️ Your payment is due in {{ $remainingTrialDays ?? '?' }} days. Please select a plan below.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Current Plan Section -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-6">Current Plan</h2>
            @if(isset($currentPlan) && $currentPlan)
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $currentPlan->name }}</h3>
                        <p class="text-gray-600 mt-2">{{ $currentPlan->description }}</p>
                        <p class="text-3xl font-bold text-blue-600 mt-4">Ksh {{ number_format($currentPlan->price) }}/month</p>
                        @if(!isset($isInTrial) || !$isInTrial)
                        <p class="text-sm text-gray-600 mt-2">
                            Expires: <strong>{{ $company->subscription_expires_at?->format('M d, Y') ?? 'Never' }}</strong>
                            ({{ $daysUntilExpiry ?? '?' }} days remaining)
                        </p>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded font-semibold">
                            Active
                        </div>
                    </div>
                </div>
            @else
                <p class="text-gray-600">No active plan. Select one below to get started.</p>
            @endif
        </div>

        <!-- Available Plans Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-6">Select a Plan</h2>
            @if((isset($isPaymentAvailable) && !$isPaymentAvailable) && (!isset($isInTrial) || !$isInTrial))
            <div class="mb-6 bg-red-50 border-l-4 border-red-600 p-6 rounded">
                <h3 class="text-lg font-bold text-red-900">Payment Not Available</h3>
                <p class="mt-1 text-red-800">
                    Your subscription expires in {{ $daysUntilExpiry ?? '?' }} days. Payment cannot be processed within 10 days of expiry.
                    <br>Please contact support if you need assistance.
                </p>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @if(isset($plans))
                @foreach($plans as $plan)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    <!-- Plan Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
                        <h3 class="text-2xl font-bold text-white">{{ $plan->name }}</h3>
                        <p class="text-blue-100 text-sm mt-2">{{ $plan->description }}</p>
                    </div>

                    <!-- Price -->
                    <div class="p-6">
                        <div class="flex items-baseline mb-6">
                            <span class="text-4xl font-bold text-blue-600">Ksh {{ number_format($plan->price) }}</span>
                            <span class="text-gray-600 ml-2">/month</span>
                        </div>

                        <!-- Features -->
                        @if(isset($plan->features) && count($plan->features) > 0)
                        <ul class="mb-6 space-y-3">
                            @foreach($plan->features as $feature)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700">{{ $feature }}</span>
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        <!-- Action Button -->
                        @if(isset($currentPlan) && $currentPlan && $currentPlan->id === $plan->id)
                        <button disabled class="w-full bg-gray-400 text-white py-3 rounded font-semibold cursor-not-allowed">
                            Current Plan
                        </button>
                        @else
                        <form method="POST" action="{{ route('admin.settings.select-plan', ['subdomain' => request()->route('subdomain')]) }}">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded font-semibold hover:bg-blue-700 transition">
                                Select Plan
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>

        <!-- Payment History -->
        @if(isset($paymentHistory) && $paymentHistory->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6">Payment History</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left">Invoice</th>
                            <th class="px-4 py-3 text-left">Plan</th>
                            <th class="px-4 py-3 text-left">Amount</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentHistory as $payment)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-blue-600">
                                <a href="{{ route('admin.settings.invoice', ['subdomain' => request()->route('subdomain'), 'payment' => $payment->id], false) }}" class="hover:underline">
                                    {{ $payment->invoice_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3">{{ $payment->plan?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-bold">Ksh {{ number_format($payment->amount) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold @if($payment->status === 'completed') bg-green-100 text-green-800 @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800 @elseif($payment->status === 'processing') bg-blue-100 text-blue-800 @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $payment->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3">
                                @if($payment->status === 'pending' && ($isPaymentAvailable ?? true))
                                    <a href="{{ route('admin.settings.payment', ['subdomain' => request()->route('subdomain'), 'payment' => $payment->id], false) }}" class="text-blue-600 hover:underline">
                                        View Invoice & Payment Details
                                    </a>
                                @else
                                    <a href="{{ route('admin.settings.invoice', ['subdomain' => request()->route('subdomain'), 'payment' => $payment->id], false) }}" class="text-blue-600 hover:underline">
                                        View Invoice & Payment Details
                                    </a>
                                    @if($payment->status === 'pending' && isset($isPaymentAvailable) && !$isPaymentAvailable)
                                        <div class="text-sm text-red-600 mt-1">Payment not available: cannot process payments within 10 days of subscription expiry.</div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
