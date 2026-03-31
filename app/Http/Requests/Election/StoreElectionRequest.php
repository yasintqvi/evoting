<?php

namespace App\Http\Requests\Election;

use App\DTOs\Election\StoreElectionRequest as StoreDto;
use App\DTOs\Election\CreateElectionDto;
use App\Enums\ElectionType;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreElectionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'min:2'],
            'type' => ['required', Rule::in(ElectionType::values())],
            'main_member_count' => ['required', 'integer', 'min:1'],
            'substitute_member_count' => ['required', 'integer', 'min:0'],
            'position_id' => ['required', 'exists:positions,id'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'ignore_stock_weight' => ['nullable', 'boolean'],
            'blocked_user_ids' => ['nullable', 'array'],
            'blocked_user_ids.*' => ['integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ends_at.after_or_equal' => 'زمان پایان باید بعد از زمان شروع باشد.',
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
            $this->validated('starts_at') ? Carbon::parse($this->validated('starts_at')) : null,
            $this->validated('ends_at') ? Carbon::parse($this->validated('ends_at')) : null,
            (bool) $this->input('ignore_stock_weight', false),
            (array) $this->input('blocked_user_ids', []),
        );
    }
}
