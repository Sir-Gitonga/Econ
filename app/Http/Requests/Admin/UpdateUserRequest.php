<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled via middleware (role:admin)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user->id ?? null;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'mobile' => 'nullable|string|max:20|unique:users,mobile,' . $userId,
            'role_id' => 'required|integer|exists:roles,id',
            'status' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'User name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email is already registered.',
            'mobile.unique' => 'This phone number is already in use.',
            'role_id.required' => 'Role selection is required.',
            'role_id.exists' => 'Selected role is invalid for this company.',
        ];
    }
}
