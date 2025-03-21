<?php

namespace App\Http\Requests\Group;

use App\Enums\ElectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class GroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'company_type' => ['required', Rule::in(array_map(fn($case) => $case->value, ElectionType::cases()))],
            'prefered_stock_weight' => ['nullable', Rule::requiredIf(fn() => in_array($this->type, [ElectionType::COOPERTAIVE->value, ElectionType::SPECIAL->value])), 'integer'],
            'sum_stock' => ['nullable', Rule::requiredIf(fn() => in_array($this->type, [ElectionType::COOPERTAIVE->value, ElectionType::SPECIAL->value])), 'integer'],
            'description' => 'nullable|string|max:500',
            'logo' => 'nullable|file|mimes:jpg,png,webp,jpeg|max:2048',
        ];
    }
}
