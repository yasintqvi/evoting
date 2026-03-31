<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'phone' => 'required|string|max:15|unique:users,phone',
            'nationalcode' => 'required|string|max:10|unique:users,nationalcode',
            // 'is_active' => 'nullable|boolean',
            'normal_stock_count' => 'nullable|integer|min:0',
            'prefered_stock_count' => 'nullable|integer|min:0',
        ];
    }
}
