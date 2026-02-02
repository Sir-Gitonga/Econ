<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\CompanySmsSetting;
use App\Models\CompanyWhatsappSetting;

class CompanyCommunicationSettingsSeeder extends Seeder
{
    public function run()
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            CompanySmsSetting::firstOrCreate(['company_id' => $company->id]);
            CompanyWhatsappSetting::firstOrCreate(['company_id' => $company->id]);
        }
    }
}
