<?php

namespace App\Http\Requests\Election;

use App\DTOs\Election\UpdateElectionDto;
use App\Enums\ElectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateElectionRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255', 'min:2'],
            'quorum_required' => ['nullable', 'in:0,1'],
            'main_member_count' => ['required', 'integer', 'min:1'],
            'substitute_member_count' => ['required', 'integer', 'min:0'],
            'incpector_main_member_count' => ['required', 'integer', 'min:0'],
            'incpector_substitute_member_count' => ['required', 'integer', 'min:0'],
        ];
    }

    public function toDto(): UpdateElectionDto
    {
        return new UpdateElectionDto(
            $this->validated('title'),
            (bool) $this->validated('quorum_required'),
            $this->validated('main_member_count'),
            $this->validated('substitute_member_count'),
            $this->validated('incpector_main_member_count'),
            $this->validated('incpector_substitute_member_count'),
        );
    }
}
