<?php

namespace App\Http\Requests\Election;


use App\Enums\ElectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreElectionRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'min:2'],
            'type' => ['nullable', Rule::in(array_map(fn($case) => $case->value, ElectionType::cases()))],
            'normal_stock_count' => ['nullable', 'numeric'],
            'prefered_stock_count' => ['nullable', 'numeric'],
            'prefered_stock_weight' => ['nullable', 'numeric'],
            'main_member_count' => ['required', 'numeric'],
            'substitute_member_count' => ['required', 'numeric'],
            'incpector_main_member_count' => ['required', 'numeric'],
            'incpector_substitute_member_count' => ['required', 'numeric'],
        ]; 
    }
}
