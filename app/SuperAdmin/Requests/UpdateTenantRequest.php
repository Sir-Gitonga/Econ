<?php

namespace App\SuperAdmin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $this->route('tenant')->id,
            'phone' => 'nullable|string|max:20',
            'domain' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'nullable|in:active,inactive,suspended',
        ];
    }
}