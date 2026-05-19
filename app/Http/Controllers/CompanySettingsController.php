<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\AppearanceSetting;
use App\Models\PaymentSetting;
use App\Models\CommunicationSetting;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanySettingsController extends Controller
{
    /**
     * Show settings page
     */
    public function index()
    {
        $company = Auth::user()->company;

        if (!$company) {
            return redirect()->route('home')->with('error', 'Company not found');
        }

        // Initialize settings if they don't exist
        $this->initializeSettings($company);

        // Get all settings
        $companySetting = $company->companySetting;
        $appearanceSetting = $company->appearanceSetting;
        $paymentSetting = $company->paymentSetting;
        $communicationSetting = $company->communicationSetting;
        $businessSetting = $company->businessSetting;
        $smsSetting = $company->smsSetting;
        $whatsappSetting = $company->whatsappSetting;

        // Billing & Subscription variables (for partial)
        $plans = \App\Models\SubscriptionPlan::all();
        $currentPlan = $company->currentPlan ?? null;
        $paymentHistory = $company->payments()->orderByDesc('created_at')->limit(10)->get();
        $isInTrial = method_exists($company, 'isInTrial') ? $company->isInTrial() : false;
        $remainingTrialDays = method_exists($company, 'getRemainingTrialDays') ? $company->getRemainingTrialDays() : null;
        $shouldShowPaymentDue = method_exists($company, 'shouldShowPaymentDueNotification') ? $company->shouldShowPaymentDueNotification() : false;
        $isPaymentAvailable = method_exists($company, 'isPaymentAvailable') ? $company->isPaymentAvailable() : false;
        $daysUntilExpiry = method_exists($company, 'getDaysUntilExpiry') ? $company->getDaysUntilExpiry() : null;

        return view('admin.settings.index', compact(
            'company',
            'companySetting',
            'appearanceSetting',
            'paymentSetting',
            'communicationSetting',
            'businessSetting',
            'smsSetting',
            'whatsappSetting',
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
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $company = Auth::user()->company;

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'timezone' => 'required|timezone',
            'currency' => 'required|string|size:3',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $companySetting = $company->companySetting;

            // Delete old logo
            if ($companySetting->logo && Storage::exists('public/' . $companySetting->logo)) {
                Storage::delete('public/' . $companySetting->logo);
            }

            // Store new logo
            $validated['logo'] = $request->file('logo')->store('company-logos', 'public');
        }

        // Update or create settings
        $company->companySetting()->updateOrCreate(
            ['company_id' => $company->id],
            $validated
        );

        return back()->with('success', 'General settings updated successfully!');
    }

    /**
     * Update appearance settings
     */
    public function updateAppearance(Request $request)
    {
        $company = Auth::user()->company;

        $validated = $request->validate([
            'primary_color' => 'required|regex:/^#[A-Fa-f0-9]{6}$/',
            'secondary_color' => 'required|regex:/^#[A-Fa-f0-9]{6}$/',
            'theme' => 'required|in:light,dark',
            'invoice_template' => 'required|in:default,modern,professional,detailed',
            'favicon' => 'nullable|image|mimes:ico,png,jpg|max:512',
        ]);

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $appearanceSetting = $company->appearanceSetting;

            // Delete old favicon
            if ($appearanceSetting->favicon && Storage::exists('public/' . $appearanceSetting->favicon)) {
                Storage::delete('public/' . $appearanceSetting->favicon);
            }

            // Store new favicon
            $validated['favicon'] = $request->file('favicon')->store('favicons', 'public');
        }

        $company->appearanceSetting()->updateOrCreate(
            ['company_id' => $company->id],
            $validated
        );

        return back()->with('success', 'Appearance settings updated successfully!');
    }

    /**
     * Update payment settings
     */
    public function updatePayment(Request $request)
    {
        $company = Auth::user()->company;

        $gateway = $request->input('gateway');

        $validated = [
            'gateway' => $request->validate(['gateway' => 'required|in:mpesa,intasend,both'])['gateway'],
        ];

        // Validate M-PESA settings if selected
        if (in_array($gateway, ['mpesa', 'both'])) {
            $mpesaRules = [
                'mpesa_paybill' => 'required|string|max:10',
                'mpesa_consumer_key' => 'required|string|max:255',
                'mpesa_consumer_secret' => 'required|string|max:255',
                'mpesa_passkey' => 'required|string|max:255',
                'mpesa_environment' => 'required|in:sandbox,live',
            ];
            $mpesaData = $request->validate($mpesaRules);
            $validated = array_merge($validated, $mpesaData);
        }

        // Validate IntaSend settings if selected
        if (in_array($gateway, ['intasend', 'both'])) {
            $intasendRules = [
                'intasend_publishable_key' => 'required|string|max:255',
                'intasend_secret_key' => 'required|string|max:255',
                'intasend_mode' => 'required|in:test,live',
            ];
            $intasendData = $request->validate($intasendRules);
            $validated = array_merge($validated, $intasendData);
        }

        $company->paymentSetting()->updateOrCreate(
            ['company_id' => $company->id],
            $validated
        );

        return back()->with('success', 'Payment settings updated successfully!');
    }

    /**
     * Update business settings
     */
    public function updateBusiness(Request $request)
    {
        $company = Auth::user()->company;

        $validated = $request->validate([
            'about_description' => 'nullable|string|max:1000',
            'mission' => 'nullable|string|max:1000',
            'vision' => 'nullable|string|max:1000',
            'services' => 'nullable|string|max:2000',
            'invoice_prefix' => 'required|string|max:10',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'vat_pin' => 'nullable|string|max:50',
            'session_timeout_minutes' => 'required|integer|min:5|max:1440',
            'two_factor_enabled' => 'nullable|boolean',
        ]);

        $company->businessSetting()->updateOrCreate(
            ['company_id' => $company->id],
            $validated
        );

        return back()->with('success', 'Business settings updated successfully!');
    }

    /**
     * Update communication settings
     */
    public function updateCommunication(Request $request)
    {
        $company = Auth::user()->company;

        $validated = $request->validate([
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_from_address' => 'nullable|email|max:255',
            'smtp_from_name' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|in:tls,ssl',
            'sms_api_key' => 'nullable|string|max:500',
            'sms_provider' => 'nullable|in:twilio,vonage,africastalking',
            'email_notifications_enabled' => 'nullable|boolean',
            'sms_notifications_enabled' => 'nullable|boolean',
        ]);

        $company->communicationSetting()->updateOrCreate(
            ['company_id' => $company->id],
            $validated
        );

        return back()->with('success', 'Communication settings updated successfully!');
    }

    /**
     * Initialize settings for a new company
     */
    private function initializeSettings(Company $company)
    {
        CompanySetting::firstOrCreate(
            ['company_id' => $company->id],
            [
                'company_name' => $company->company_name,
                'email' => $company->email,
                'phone' => $company->phone,
                'address' => $company->address,
            ]
        );

        AppearanceSetting::firstOrCreate(['company_id' => $company->id]);
        PaymentSetting::firstOrCreate(['company_id' => $company->id]);
        CommunicationSetting::firstOrCreate(['company_id' => $company->id]);
        BusinessSetting::firstOrCreate(['company_id' => $company->id]);

        // SMS & WhatsApp settings (ensure rows exist)
        \App\Models\CompanySmsSetting::firstOrCreate(['company_id' => $company->id]);
        \App\Models\CompanyWhatsappSetting::firstOrCreate(['company_id' => $company->id]);
    }
}
