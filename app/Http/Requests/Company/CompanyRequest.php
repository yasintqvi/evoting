<?php

namespace App\Http\Requests\Company;

use App\Enums\CompanyType;
use App\Enums\ElectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;



class CompanyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'type' => ['required', 'in:' . implode(',', CompanyType::values())],
            'description' => ['nullable', 'string', 'max:500'],
            'logo' => 'nullable|file|mimes:jpg,png,webp,jpeg|max:2048',
        ];

        if ($this->input('type') == CompanyType::SPECIAL->value) {
            $rules['normal_stock_count'] = ['required', 'integer', 'min:1'];
            $rules['prefered_stock_count'] = ['required', 'integer', 'min:0'];
            $rules['prefered_stock_weight'] = ['required', 'numeric', 'min:0'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'type.required' => 'لطفاً نوع شرکت را انتخاب کنید.',
            'type.in' => 'نوع شرکت انتخاب‌شده معتبر نیست.',
            'normal_stock_count.required' => 'تعداد سهام عادی الزامی است.',
            'normal_stock_count.integer' => 'تعداد سهام عادی باید عدد صحیح باشد.',
            'normal_stock_count.min' => 'تعداد سهام عادی باید حداقل ۱ باشد.',
            'prefered_stock_count.required' => 'تعداد سهام ممتاز الزامی است.',
            'prefered_stock_count.integer' => 'تعداد سهام ممتاز باید عدد صحیح باشد.',
            'prefered_stock_count.min' => 'تعداد سهام ممتاز نمی‌تواند منفی باشد.',
            'prefered_stock_weight.required' => 'وزن سهام الزامی است.',
            'prefered_stock_weight.numeric' => 'وزن سهام باید عددی باشد.',
            'prefered_stock_weight.min' => 'وزن سهام نمی‌تواند منفی باشد.',
        ];
    }
}
