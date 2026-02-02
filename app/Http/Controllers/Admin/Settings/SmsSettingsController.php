<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\CompanySmsSetting;

class SmsSettingsController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;
        $smsSetting = CompanySmsSetting::firstOrCreate(['company_id' => $company->id]);

        return view('admin.settings.index', compact('smsSetting'));
    }

    public function storeOrUpdate(Request $request)
    {
        $company = Auth::user()->company;

        $provider = $request->input('provider');

        $rules = ['provider' => 'required|in:advanta,africas_talking,ajookatt,beem,blessed_texts'];

        // Dynamic rules
        switch ($provider) {
            case 'advanta':
                $rules = array_merge($rules, [
                    'api_key' => 'required|string',
                    'partner_id' => 'required|string',
                    'sender_id' => 'required|string',
                ]);
                break;
            case 'africas_talking':
                $rules = array_merge($rules, [
                    'username' => 'required|string',
                    'api_key' => 'required|string',
                    'sender_id' => 'required|string',
                ]);
                break;
            case 'ajookatt':
                $rules = array_merge($rules, [
                    'api_key' => 'required|string',
                    'sender_id' => 'required|string',
                ]);
                break;
            case 'beem':
                $rules = array_merge($rules, [
                    'username' => 'required|string',
                    'api_key' => 'required|string',
                    'sender_id' => 'required|string',
                ]);
                break;
            case 'blessed_texts':
                $rules = array_merge($rules, [
                    'username' => 'required|string',
                    'sender_id' => 'required|string',
                ]);
                break;
        }

        $validated = $request->validate($rules);

        // Prepare data for storage, encrypt sensitive ones
        $data = [
            'provider' => $provider,
            'partner_id' => $validated['partner_id'] ?? null,
            'sender_id' => $validated['sender_id'] ?? null,
        ];

        // Store encrypted fields only when present
        if (isset($validated['username'])) {
            $data['username'] = Crypt::encryptString($validated['username']);
        }
        if (isset($validated['api_key'])) {
            $data['api_key'] = Crypt::encryptString($validated['api_key']);
        }

        CompanySmsSetting::updateOrCreate(
            ['company_id' => $company->id],
            $data
        );

        return back()->with('success', 'SMS settings saved successfully!');
    }
}
