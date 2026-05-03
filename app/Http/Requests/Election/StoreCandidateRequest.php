<?php

namespace App\Http\Requests\Election;

use App\DTOs\Election\ElectionCandidatesDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
{
    public function rules(): array
    {
        $election = $this->route('election');
        $requiredCandidatesCount = (int) ($election?->candidate_count ?? 0);
        if ($requiredCandidatesCount <= 0) {
            $requiredCandidatesCount = (int) ($election?->main_member_count ?? 0);
        }

        return [
            'main_candidates_ids' => [
                'required',
                'array',
                'distinct',
                $requiredCandidatesCount > 0 ? ('size:' . $requiredCandidatesCount) : ('min:' . ($election->main_member_count ?? 0)),
            ],
            'main_candidates_ids.*' => ['exists:users,id'],

            // 'incpector_candidates_ids' => [
            //     'required',
            //     'array',
            //     'distinct',
            //     'min:' . ($election->incpector_main_member_count ?? 0),
            // ],
            // 'incpector_candidates_ids.*' => ['exists:users,id'],
        ];
    }

    public function messages(): array
    {
        $election = $this->route('election');
        $requiredCandidatesCount = (int) ($election?->candidate_count ?? 0);

        return [
            'main_candidates_ids.size' => $requiredCandidatesCount > 0
                ? "باید دقیقاً {$requiredCandidatesCount} نامزد انتخاب کنید."
                : 'تعداد انتخاب‌شده معتبر نیست.',
        ];
    }

    public function toDto(): ElectionCandidatesDto
    {
        return new ElectionCandidatesDto(
            $this->validated('main_candidates_ids'),
            // $this->validated('incpector_candidates_ids')
        );
    }
}
