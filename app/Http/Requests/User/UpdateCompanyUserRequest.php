<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateCompanyUserRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id; 
    
        $rules = [
            'first_name'           => ['required', 'string', 'max:255'],
            'last_name'            => ['required', 'string', 'max:255'],
            'phone'                => ['required', 'numeric', 'digits:11', "unique:users,phone,{$userId}"],
            'nationalcode'         => ['required', 'numeric', 'digits:10', "unique:users,nationalcode,{$userId}"],
            'is_active'            => ['sometimes', 'boolean'], 
        ];
    
        if ($this->company->type == \App\Enums\CompanyType::SPECIAL) {
            $rules['normal_stock_count'] = ['required', 'integer', 'min:1'];
            $rules['prefered_stock_count'] = ['required', 'integer', 'min:1'];

            $rules['total_stocks'] = [
                function ($attribute, $value, $fail) {
                    $totalRequested = 
                        ($this->prefered_stock_count * $this->company->prefered_stock_weight) 
                        + $this->normal_stock_count;
        
                    if ($totalRequested > $this->company->remaining_weighted_stocks) {
                        $fail('مجموع سهام درخواستی (با احتساب وزن) بیشتر از موجودی شرکت است');
                    }
                }
            ];
        }
        return $rules;  
    }
    
}
