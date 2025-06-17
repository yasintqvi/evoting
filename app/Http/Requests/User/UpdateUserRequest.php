<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


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
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'phone'         => ['required', 'string', 'max:11', Rule::unique('users', 'phone')->ignore($this->user->id)],
            'nationalcode'  => ['required', 'string', 'max:10', Rule::unique('users', 'nationalcode')->ignore($this->user->id)],
            'is_active'     => 'sometimes|boolean',
        ];
    }

}
