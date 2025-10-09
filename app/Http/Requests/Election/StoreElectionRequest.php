<?php

namespace App\Http\Requests\Election;

use App\DTOs\Election\CreateElectionDto;
use App\Enums\ElectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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
            'main_member_count' => ['required', 'integer', 'min:1'],
            'substitute_member_count' => ['required', 'integer', 'min:0'],
            'position_id' => ['required', 'exists:positions,id'],
        ];
    }

    public function toDto(): CreateElectionDto
    {
        return new CreateElectionDto(
            $this->validated('title'),
            Auth::user()->getAuthIdentifier(),
            $this->validated('position_id'),
            ElectionType::from($this->validated('type')),
            $this->validated('main_member_count'),
            $this->validated('substitute_member_count'),
        );
    }
}
