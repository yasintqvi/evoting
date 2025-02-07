<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
    public function rules()
    {
        if ($this->has('user_ids')) {
            return [
                'group_ids' => 'required|array|min:1',
                'group_ids.*' => 'exists:groups,id',
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id',
            ];
        } else {
            return [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20|unique:users,phone',
                'group_ids' => 'required|array|min:1',
                'group_ids.*' => 'exists:groups,id',
            ];
        }
    }
}
