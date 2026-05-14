<?php

namespace App\Http\Requests\Election;

use App\DTOs\Election\ElectionCandidatesDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
{
    public function rules(): array
    {
        $election = $this->route('election');
        $seatTotal = (int) ($election?->main_member_count ?? 0) + (int) ($election?->substitute_member_count ?? 0);
        $minCandidates = $seatTotal > 0 ? $seatTotal : 1;

        return [
            'main_candidates_ids' => [
                'required',
                'array',
                'distinct',
                'min:'.$minCandidates,
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
        $seatTotal = (int) ($election?->main_member_count ?? 0) + (int) ($election?->substitute_member_count ?? 0);

        return [
            'main_candidates_ids.min' => $seatTotal > 0
                ? 'حداقل باید به‌اندازهٔ تعداد صندلی‌های این همه‌پرسی ('.$seatTotal.' نفر) نامزد انتخاب کنید. می‌توانید بیشتر هم انتخاب کنید.'
                : 'حداقل یک نامزد را انتخاب کنید.',
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
