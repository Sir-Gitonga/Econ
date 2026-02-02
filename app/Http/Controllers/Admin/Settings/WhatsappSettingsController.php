<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\CompanyWhatsappSetting;

class WhatsappSettingsController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;
        $whatsappSetting = CompanyWhatsappSetting::firstOrCreate(['company_id' => $company->id]);

        return view('admin.settings.index', compact('whatsappSetting'));
    }

    public function storeOrUpdate(Request $request)
    {
        $company = Auth::user()->company;

        $gateway = $request->input('gateway');

        $rules = ['gateway' => 'required|in:apiwap,infobip,twilio'];

        // Dynamic rules
        switch ($gateway) {
            case 'apiwap':
                $rules = array_merge($rules, [
                    'api_key' => 'required|string',
                    'instance_id' => 'required|string',
                ]);
                break;
            case 'infobip':
                $rules = array_merge($rules, [
                    'api_key' => 'required|string',
                    'base_url' => 'required|url',
                ]);
                break;
            case 'twilio':
                $rules = array_merge($rules, [
                    'account_sid' => 'required|string',
                    'auth_token' => 'required|string',
                    'from_number' => 'required|string',
                ]);
                break;
        }

        $validated = $request->validate($rules);

        $data = [
            'gateway' => $gateway,
            'instance_id' => $validated['instance_id'] ?? null,
            'base_url' => $validated['base_url'] ?? null,
            'from_number' => $validated['from_number'] ?? null,
        ];

        if (isset($validated['api_key'])) {
            $data['api_key'] = Crypt::encryptString($validated['api_key']);
        }
        if (isset($validated['account_sid'])) {
            $data['account_sid'] = Crypt::encryptString($validated['account_sid']);
        }
        if (isset($validated['auth_token'])) {
            $data['auth_token'] = Crypt::encryptString($validated['auth_token']);
        }

        CompanyWhatsappSetting::updateOrCreate(
            ['company_id' => $company->id],
            $data
        );

        return back()->with('success', 'WhatsApp settings saved successfully!');
    }
}
