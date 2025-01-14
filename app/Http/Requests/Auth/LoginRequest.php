<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\LoginDTO;
use App\Enums\AuthType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function prepareForValidation(): void
    {
        $this->merge([
            'identifier' => ed($this->input('identifier')),
            'password' => ed($this->input('password')) ?? null,
            'otp' => ed($this->input('otp')) ?? null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $identifier_field = config('auth.identifier');

        return [
            'auth_type' => ['required', Rule::in(AuthType::getTypes())],
            'identifier' => ['required', 'string', Rule::exists('users', $identifier_field)],
            'password' => [Rule::requiredIf((int) $this->auth_type === AuthType::PASSWORD->value)],
            'otp' => [Rule::requiredIf((int) $this->auth_type === AuthType::OTP->value)],
            'remember' => ['nullable', 'in:0,1'],
        ];
    }

    public function toDTO(): LoginDTO
    {
        return new LoginDTO(
            $this->validated('identifier'),
            (int) $this->validated('auth_type'),
            $this->validated('password'),
            $this->validated('otp'),
            (bool) $this->validated('remember')
        );
    }
}
