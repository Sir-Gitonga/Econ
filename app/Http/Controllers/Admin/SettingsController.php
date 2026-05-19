<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SubscriptionPlan;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * SettingsController
 * 
 * Handles billing, subscription, and settings for admin users
 */
class SettingsController extends Controller
{
    /**
     * Show billing & subscription settings
     */
    public function billing()
    {
        $company = Auth::user()->company;
        $plans = SubscriptionPlan::all();
        $currentPlan = $company->currentPlan;
        
        // Get payment history
        $paymentHistory = $company->payments()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        
        // Calculate trial info
        $isInTrial = $company->isInTrial();
        $remainingTrialDays = $company->getRemainingTrialDays();
        $shouldShowPaymentDue = $company->shouldShowPaymentDueNotification();
        $isPaymentAvailable = $company->isPaymentAvailable();
        $daysUntilExpiry = $company->getDaysUntilExpiry();
        
        return view('admin.settings.billing', compact(
            'company',
            'plans',
            'currentPlan',
            'paymentHistory',
            'isInTrial',
            'remainingTrialDays',
            'shouldShowPaymentDue',
            'isPaymentAvailable',
            'daysUntilExpiry'
        ));
    }

    /**
     * Select a subscription plan
     */
    public function selectPlan(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $company = Auth::user()->company;
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        // Create payment for this plan
        $payment = Payment::create([
            'company_id' => $company->id,
            'subscription_plan_id' => $plan->id,
            'invoice_number' => Payment::generateInvoiceNumber(),
            'amount' => $plan->price,
            'status' => 'pending',
            'payment_method' => 'mpesa',
            'payment_for_period_start' => now(),
            'payment_for_period_end' => now()->addMonth(),
        ]);

        // Redirect to payment view
        return redirect()->route('admin.settings.payment', $payment->id);
    }

    /**
     * Show payment page
     */
    public function payment($payment)
    {
        $payment = Payment::findOrFail($payment);

        // Ensure company owns this payment
        if ($payment->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $company = Auth::user()->company;
        $isPaymentAvailable = $company->isPaymentAvailable();
        $daysUntilExpiry = $company->getDaysUntilExpiry();

        return view('admin.settings.payment', compact(
            'payment',
            'company',
            'isPaymentAvailable',
            'daysUntilExpiry'
        ));
    }

    /**
     * Process M-Pesa payment
     */
    public function processPayment(Request $request, $payment)
    {
        $payment = Payment::findOrFail($payment);

        // Ensure company owns this payment
        if ($payment->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'mpesa_phone' => 'required|regex:/^254\d{9}$/',
        ], [
            'mpesa_phone.regex' => 'M-Pesa phone number must be in format 254xxxxxxxxx',
        ]);

        $company = Auth::user()->company;

        // Check if payment is still available
        if (!$company->isPaymentAvailable()) {
            return back()->with('error', 'Cannot process payment within 10 days of subscription expiry');
        }

        // Update payment with M-Pesa phone
        $payment->update([
            'mpesa_phone' => $validated['mpesa_phone'],
            'status' => 'processing',
        ]);

        // TODO: Integrate with M-Pesa API for STK PUSH
        // For now, we'll show a message indicating payment is being processed
        
        return redirect()->route('admin.settings.payment', $payment->id)
            ->with('success', 'Payment initiated. Please check your phone for M-Pesa prompt.');
    }

    /**
     * Show invoice details
     */
    public function invoice($payment)
    {
        $payment = Payment::findOrFail($payment);

        // Ensure company owns this payment
        if ($payment->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return view('admin.settings.invoice', compact('payment'));
    }

    /**
     * Download invoice as PDF
     */
    public function downloadInvoice($payment)
    {
        $payment = Payment::findOrFail($payment);

        // Ensure company owns this payment
        if ($payment->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        // TODO: Generate PDF invoice
        // For now, return the view
        return view('admin.settings.invoice-pdf', compact('payment'));
    }

    /**
     * Mark trial notification as shown
     */
    public function markPaymentNotificationSeen()
    {
        $company = Auth::user()->company;
        $company->update(['trial_notified_day_25' => true]);
        
        return response()->json(['success' => true]);
    }
}
