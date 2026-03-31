<?php

namespace App\Http\Requests\Profile;

use App\Enums\TwoFactorType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|string|max:255',
            'avatar' => 'nullable|file|mimes:jpg,png,jpeg,webp|max:2048',
            'two_factor_type' => ['nullable', Rule::in(array_map(fn ($value) => $value, TwoFactorType::cases()))],
        ];
    }
}
