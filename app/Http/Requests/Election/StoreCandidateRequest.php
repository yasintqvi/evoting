<?php

namespace App\Http\Requests\Election;

use App\Models\Election;
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
                'min:' . $election->main_member_count
            ],
            'main_candidates_ids.*' => ['exists:users,id'],

            'incpector_candidates_ids' => [
                'required',
                'array',
                'distinct',
                'min:' . $election->incpector_main_member_count
            ],
            'incpector_candidates_ids.*' => ['exists:users,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $mainCandidates = $this->input('main_candidates_ids', []);
            $incpectorCandidates = $this->input('incpector_candidates_ids', []);

            $duplicateCandidates = array_intersect($mainCandidates, $incpectorCandidates);
            if (!empty($duplicateCandidates)) {
                $validator->errors()->add('main_candidates_ids', 'یک نامزد نمی‌تواند همزمان در هیئت مدیره و بازرس باشد.');
            }
        });
    }
}
