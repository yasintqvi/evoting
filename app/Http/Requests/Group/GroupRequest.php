<?php

namespace App\Http\Requests\Group;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'type' => [$this->isMethod('post') ? 'required' : 'nullable'],
            'description' => ['nullable', 'string', 'max:500'],
            'logo' => 'nullable|file|mimes:jpg,png,webp,jpeg|max:2048',
        ];

        // فیلدهای سهام برای همه نوع گروه‌ها قابل ویرایش هستن
        $rules['normal_stock_count'] = ['nullable', 'integer', 'min:0'];
        $rules['prefered_stock_count'] = ['nullable', 'integer', 'min:0'];
        $rules['prefered_stock_weight'] = ['nullable', 'numeric', 'min:0'];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'type.required' => 'لطفاً نوع گروه را انتخاب کنید.',
            'type.in' => 'نوع گروه انتخاب‌شده معتبر نیست.',
            'normal_stock_count.integer' => 'تعداد سهام عادی باید عدد صحیح باشد.',
            'normal_stock_count.min' => 'تعداد سهام عادی نمی‌تواند منفی باشد.',
            'prefered_stock_count.integer' => 'تعداد سهام ممتاز باید عدد صحیح باشد.',
            'prefered_stock_count.min' => 'تعداد سهام ممتاز نمی‌تواند منفی باشد.',
            'prefered_stock_weight.numeric' => 'وزن سهام باید عددی باشد.',
            'prefered_stock_weight.min' => 'وزن سهام نمی‌تواند منفی باشد.',
        ];
    }
}
