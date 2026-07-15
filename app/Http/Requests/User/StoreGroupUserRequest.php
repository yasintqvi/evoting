<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreGroupUserRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? 1 : 0,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'numeric', 'digits:11'],
            'nationalcode' => ['required', 'numeric', 'digits:10'],
            'is_active' => ['sometimes', 'boolean'],
            'avatar' => ['nullable', 'file', 'mimes:jpg,png,jpeg,webp', 'max:2048'],
        ];

        if ($this->group->type == \App\Enums\GroupType::SPECIAL) {
            $rules['normal_stock_count'] = [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($value > $this->group->total_normal_stock) {
                        $fail('مقدار سهام عادی بیشتر از مقدار باقیمانده است.');
                    }
                },
            ];

            $rules['prefered_stock_count'] = [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($value > $this->group->total_prefered_stock) {
                        $fail('مقدار سهام ممتاز بیشتر از مقدار باقیمانده است.');
                    }
                },
            ];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->group->type == \App\Enums\GroupType::SPECIAL) {
                if ((int) $this->input('normal_stock_count') === 0 && (int) $this->input('prefered_stock_count') === 0) {
                    $validator->errors()->add('normal_stock_count', 'حداقل یکی از سهام عادی یا ممتاز باید بیشتر از صفر باشد.');
                }
            }
        });
    }
}
