<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function prepareForValidation()
    {
        if ($this->has('phone')) {
            $this->merge([
                'phone' => ed($this->input('phone')),
                'is_active' => $this->has('is_active') ? 1 : 0,

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
        if ($this->has('phone')) {
            return [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|numeric|digits:11|unique:users,phone',
                'is_active' => 'sometimes|boolean',
            ];
        } else {
            return [
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id',
            ];
        }
    }
}
