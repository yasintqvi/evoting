<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->filled('phone')) {
            $this->merge([
                'phone' => ed($this->input('phone')),
                'is_active' => $this->has('is_active') ? 1 : 0,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */



    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:11|min:11|unique:users,phone',
            'nationalcode' => 'required|string|max:10|unique:users,nationalcode',
            'is_active' => 'sometimes|boolean',
        ];
    }



}
