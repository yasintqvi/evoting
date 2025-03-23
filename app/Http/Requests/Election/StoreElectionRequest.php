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
            'type' => ['required', Rule::in(ElectionType::values())],
            'quorum_required' => ['nullable', 'in:0,1'],
            'prefered_stock_weight' => ['nullable', Rule::requiredIf(fn() => in_array($this->type, [ElectionType::PRIVATE_JOINT->value, ElectionType::PRIVATE_JOINT_WITH_88->value])), 'integer'],
            'prefered_stock_count' => ['nullable', Rule::requiredIf(fn() => in_array($this->type, [ElectionType::PRIVATE_JOINT->value, ElectionType::PRIVATE_JOINT_WITH_88->value])), 'integer', 'min:1'],
            'normal_stock_count' => ['nullable', Rule::requiredIf(fn() => in_array($this->type, [ElectionType::PRIVATE_JOINT->value, ElectionType::PRIVATE_JOINT_WITH_88->value])), 'integer', 'min:1'],
            'main_member_count' => ['required', 'integer', 'min:1'],
            'substitute_member_count' => ['required', 'integer', 'min:0'],
            'incpector_main_member_count' => ['required', 'integer', $this->type == ElectionType::PUBLIC_JOINT->value ? 'min:0' : 'min:1'],
            'incpector_substitute_member_count' => ['required', 'integer', 'min:0'],
        ];
    }
}
