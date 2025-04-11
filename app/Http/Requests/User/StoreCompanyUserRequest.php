<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
            $rules = [
                'first_name'           => ['required', 'string', 'max:255'],
                'last_name'            => ['required', 'string', 'max:255'],
                'phone'                => ['required', 'numeric', 'digits:11', 'unique:users,phone'],
                'nationalcode'         => ['required', 'numeric', 'digits:10', 'unique:users,nationalcode'],
                'is_active'            => ['sometimes', 'boolean'], 
            ];

            if ($this->company->type == \App\Enums\CompanyType::SPECIAL) {
                $rules['normal_stock_count'] = [
                    'required',
                    'integer',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        if ($value > $this->company->total_normal_stock) {
                            $fail('مقدار سهام عادی بیشتر از مقدار باقیمانده است.');
                        }
                    }
                ];
                
                $rules['prefered_stock_count'] = [
                    'required',
                    'integer',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        if ($value > $this->company->total_prefered_stock) {
                            $fail('مقدار سهام ممتاز بیشتر از مقدار باقیمانده است.');
                        }
                    }
                ];
             }
            return $rules;  
    }
  
}
