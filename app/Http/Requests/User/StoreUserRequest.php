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
                'phone'     => ed($this->input('phone')),
                'is_active' => $this->has('is_active') ? 1 : 0,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return $this->filled('phone') ? $this->getStoreRules() : $this->getAssignRules();
    }


    protected function getStoreRules(): array
    {
        return [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'phone'        => 'required|string|max:11|unique:users,phone',
            'nationalcode' => 'required|string|max:10|unique:users,nationalcode',
            'company_ids'  => 'required|array',
            'company_ids.*'=> 'exists:companies,id',
            'is_active'    => 'sometimes|boolean',
        ];
    }

 
    protected function getAssignRules(): array
    {
        return [
            'user_ids'     => 'required|array',
            'user_ids.*'   => 'exists:users,id',
            'company_ids'  => 'required|array',
            'company_ids.*'=> 'exists:companies,id',
        ];
    }
}
