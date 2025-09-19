<?php

namespace App\Http\Requests\Election;

use App\DTOs\Election\ElectionCandidatesDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
{
    public function rules(): array
    {
        $election = $this->route('election');

        return [
            'main_candidates_ids' => [
                'required',
                'array',
                'distinct',
                'min:'.$election->main_member_count,
            ],
            'main_candidates_ids.*' => ['exists:users,id'],

            'incpector_candidates_ids' => [
                'required',
                'array',
                'distinct',
                'min:'.$election->incpector_main_member_count,
            ],
            'incpector_candidates_ids.*' => ['exists:users,id'],
        ];
    }

    public function toDto(): ElectionCandidatesDto
    {
        return new ElectionCandidatesDto(
            $this->validated('main_candidates_ids'),
            $this->validated('incpector_candidates_ids')
        );
    }
}
