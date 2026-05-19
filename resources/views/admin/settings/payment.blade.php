@extends('layouts.app')

@section('page-title', 'Payment - ' . $payment->invoice_number)

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('admin.settings.billing', [], false) }}" class="text-blue-600 hover:underline flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Billing
            </a>
            <h1 class="text-4xl font-bold text-gray-900 mt-4">Payment</h1>
            <p class="text-gray-600 mt-2">Invoice: <strong>{{ $payment->invoice_number }}</strong></p>
        </div>

        <!-- Payment Status Alert -->
        @if($payment->status === 'completed')
        <div class="mb-6 bg-green-50 border-l-4 border-green-600 p-6 rounded">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-green-900">Payment Completed</h3>
                    <p class="mt-1 text-green-800">Your payment was successfully processed on {{ $payment->paid_at->format('M d, Y H:i') }}</p>
                    <p class="mt-2 text-green-800">Receipt: <strong>{{ $payment->mpesa_receipt_number }}</strong></p>
                </div>
            </div>
        </div>
        @elseif($payment->status === 'pending' && !$isPaymentAvailable)
        <div class="mb-6 bg-red-50 border-l-4 border-red-600 p-6 rounded">
            <h3 class="text-lg font-bold text-red-900">❌ Payment Not Available</h3>
            <p class="mt-2 text-red-800">
                Your subscription expires in <strong>{{ $daysUntilExpiry }} days</strong>. 
                <br>To protect payments and ensure proper billing cycles, we cannot process payments within 10 days of subscription expiry.
            </p>
            <p class="mt-3 text-red-800">
                <strong>Next Steps:</strong>
                <ul class="list-disc ml-5 mt-2">
                    <li>Wait until 10+ days before your subscription expires</li>
                    <li>Then return here to process your payment</li>
                    <li>Contact support if you need assistance extending your timeline</li>
                </ul>
            </p>
                <a href="{{ route('admin.settings.billing', [], false) }}" class="mt-4 inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Back to Billing
            </a>
        </div>
        @elseif($payment->status === 'processing')
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-600 p-6 rounded">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 animate-spin" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 5.293a1 1 0 011.414 0A7 7 0 017 13h6a1 1 0 110 2H7a9 9 0 01-2.707-13.414z" clip-rule="evenodd" />
                </svg>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-blue-900">Payment Processing</h3>
                    <p class="mt-1 text-blue-800">Your M-Pesa payment is being processed. Please check your phone for the M-Pesa prompt.</p>
                    <p class="mt-2 text-blue-700 text-sm">M-Pesa Phone: <strong>{{ substr_replace($payment->mpesa_phone, '****', 5, 4) }}</strong></p>
                </div>
            </div>
        </div>
        @elseif($payment->status === 'failed')
        <div class="mb-6 bg-red-50 border-l-4 border-red-600 p-6 rounded">
            <h3 class="text-lg font-bold text-red-900">❌ Payment Failed</h3>
            <p class="mt-1 text-red-800">Your payment could not be processed. Please try again or contact support.</p>
        </div>
        @endif

        <!-- Invoice Details Card -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h2 class="text-2xl font-bold mb-6">Invoice Details</h2>

            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <p class="text-gray-600 text-sm">Subscription Plan</p>
                    <p class="text-xl font-bold">{{ $payment->plan->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Invoice Date</p>
                    <p class="text-xl font-bold">{{ $payment->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Period</p>
                    <p class="text-lg">{{ $payment->payment_for_period_start->format('M d') }} - {{ $payment->payment_for_period_end->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Status</p>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold @if($payment->status === 'completed') bg-green-100 text-green-800 @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800 @elseif($payment->status === 'processing') bg-blue-100 text-blue-800 @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
            </div>

            <!-- Amount Breakdown -->
            <div class="border-t border-b py-6 mb-6">
                <div class="flex justify-between mb-4">
                    <span class="text-gray-700">Subscription (1 month)</span>
                    <span class="font-semibold">Ksh {{ number_format($payment->amount) }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold text-blue-600">
                    <span>Total Amount Due</span>
                    <span>Ksh {{ number_format($payment->amount) }}</span>
                </div>
            </div>

            <!-- Payment Method Section -->
            @if($payment->status === 'pending' && $isPaymentAvailable)
            <div class="mt-8">
                <h3 class="text-xl font-bold mb-4">Payment Method</h3>
                <form method="POST" action="{{ route('admin.settings.process-payment', ['subdomain' => request()->route('subdomain'), 'payment' => $payment->id], false) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">M-Pesa Phone Number</label>
                        <input type="text" name="mpesa_phone" value="{{ old('mpesa_phone') }}" placeholder="254xxxxxxxxx" 
                               class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 @error('mpesa_phone') border-red-500 @enderror"
                               required>
                        <p class="text-gray-600 text-sm mt-1">Format: 254 followed by 9 digits (e.g., 254712345678)</p>
                        @error('mpesa_phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-600 p-4">
                        <p class="text-blue-800 text-sm">
                            <strong>How it works:</strong><br>
                            1. Enter your M-Pesa phone number<br>
                            2. You'll receive an M-Pesa prompt on your phone<br>
                            3. Enter your M-Pesa PIN to complete payment<br>
                            4. Your subscription will be activated automatically
                        </p>
                    </div>

                    <button type="button" data-confirm="payment" data-message="Pay Ksh {{ number_format($payment->amount) }} for {{ $payment->plan->name }} subscription?" 
                            class="w-full bg-green-600 text-white py-3 rounded font-semibold hover:bg-green-700">
                        Proceed with Payment
                    </button>

                    <a href="{{ route('admin.settings.billing', [], false) }}" class="block w-full text-center bg-gray-300 text-gray-900 py-3 rounded font-semibold hover:bg-gray-400">
                        Cancel
                    </a>
                </form>
            </div>
            @elseif($payment->status === 'completed')
            <div class="mt-8 flex gap-4">
                <a href="{{ route('admin.settings.download-invoice', ['subdomain' => request()->route('subdomain'), 'payment' => $payment->id], false) }}" class="flex-1 bg-blue-600 text-white text-center py-3 rounded font-semibold hover:bg-blue-700">
                    Download Invoice
                </a>
                <a href="{{ route('admin.settings.billing', [], false) }}" class="flex-1 bg-gray-300 text-gray-900 text-center py-3 rounded font-semibold hover:bg-gray-400">
                    Back to Billing
                </a>
            </div>
            @else
            <div class="mt-8">
                <a href="{{ route('admin.settings.billing', [], false) }}" class="w-full block text-center bg-gray-300 text-gray-900 py-3 rounded font-semibold hover:bg-gray-400">
                    Back to Billing
                </a>
            </div>
            @endif
        </div>

        <!-- Need Help? -->
        <div class="bg-gray-100 rounded-lg p-6 text-center">
            <p class="text-gray-700">Need help?</p>
            <p class="text-gray-600 text-sm mt-2">Contact our support team at <strong>support@econ.local</strong></p>
        </div>

    </div>
</div>

<!-- Custom confirmation handler for payment -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentBtn = document.querySelector('[data-confirm="payment"]');
        if (paymentBtn) {
            paymentBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const message = this.dataset.message;
                
                Swal.fire({
                    title: 'Confirm Payment',
                    text: message,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Pay Now',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                });
            });
        }
    });
</script>
@endsection
