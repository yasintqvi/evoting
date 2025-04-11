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
            $currentUserId = $this->user->id; 
            
            $rules['normal_stock_count'] = [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($currentUserId) {
                    $assignedToOthers = $this->company->users()
                        ->where('users.id', '!=', $currentUserId)
                        ->sum('user_company.normal_stock_count');
                        
                    $remaining = $this->company->normal_stock_count - $assignedToOthers;
                    
                    if ($value > $remaining) {
                        $fail('مقدار سهام عادی بیشتر از مقدار باقیمانده (' . $remaining . ') است.');
                    }
                }
            ];
            
            $rules['prefered_stock_count'] = [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($currentUserId) {
                    $assignedToOthers = $this->company->users()
                        ->where('users.id', '!=', $currentUserId)
                        ->sum('user_company.prefered_stock_count');
                        
                    $remaining = $this->company->prefered_stock_count - $assignedToOthers;
                    
                    if ($value > $remaining) {
                        $fail('مقدار سهام ممتاز بیشتر از مقدار باقیمانده (' . $remaining . ') است.');
                    }
                }
            ];
        }
        return $rules;  
    }
    
}
