<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function prepareForValidation()
    {
        if ($this->has('phone')) {
            $this->merge([
                'phone' => ed($this->input('phone')),
            ]);
        }
    }

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
            'phone' => 'required|string|max:15|unique:users,phone,' . $this->user->id,
            'company_ids' => 'nullable|array',
            'company_ids.*' => 'exists:groups,id',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
