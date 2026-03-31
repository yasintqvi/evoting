<?php

namespace App\Http\Requests\Election;

use App\DTOs\Election\UpdateElectionDto;
use App\Enums\ElectionType;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateElectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'min:2'],
            'quorum_required' => ['nullable', 'in:0,1'],
            'main_member_count' => ['required', 'integer', 'min:1'],
            'substitute_member_count' => ['required', 'integer', 'min:0'],
            'type' => ['required', Rule::in(ElectionType::values())],
            'position_id' => ['required', 'exists:positions,id'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'ignore_stock_weight' => ['nullable', 'boolean'],
            'blocked_users' => ['nullable', 'array'],
            'blocked_users.*' => ['exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ends_at.after_or_equal' => 'زمان پایان باید بعد از زمان شروع باشد.',
        ];
    }

    public function toDto(): UpdateElectionDto
    {
        return new UpdateElectionDto(
            $this->validated('title'),
            (bool) $this->validated('quorum_required'),
            Auth::user()->getAuthIdentifier(),
            $this->validated('position_id'),
            ElectionType::from($this->validated('type')),
            $this->validated('main_member_count'),
            $this->validated('substitute_member_count'),
            $this->validated('starts_at') ? Carbon::parse($this->validated('starts_at')) : null,
            $this->validated('ends_at') ? Carbon::parse($this->validated('ends_at')) : null,
            (bool) $this->input('ignore_stock_weight', false),
            $this->input('blocked_users', []),
        );
    }
}
