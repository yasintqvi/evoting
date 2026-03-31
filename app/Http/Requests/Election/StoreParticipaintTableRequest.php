<?php

namespace App\Http\Requests\Election;

use Illuminate\Foundation\Http\FormRequest;

class StoreParticipaintTableRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'participants.*.user_id' => 'required|exists:users,id',
        ];

        if ($this->electionTypeRequiresStock($this->route('election.type'))) {
            $rules['participants.*.normal_stock_count'] = 'nullable|integer|min:0';
            $rules['participants.*.prefered_stock_count'] = 'nullable|integer|min:0';
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $participants = $this->input('participants', []);
            $election = $this->route('election');

            $totalNormalStocks = 0;
            $totalPreferedStocks = 0;

            foreach ($participants as $participant) {
                $totalNormalStocks += $participant['normal_stock_count'] ?? 0;
                $totalPreferedStocks += $participant['prefered_stock_count'] ?? 0;
            }

            if ($totalNormalStocks != $election->normal_stock_count) {
                $validator->errors()->add('participants', 'تعداد سهام عادی وارد شده با تعداد کل سهام عادی برابر نیست.');
            }

            if ($totalPreferedStocks != $election->prefered_stock_count) {
                $validator->errors()->add('participants', 'تعداد سهام ممتاز وارد شده با تعداد کل سهام ممتاز برابر نیست.');
            }

            $minParticipants = $election->main_member_count;
            if (count($participants) < $minParticipants) {
                $validator->errors()->add('participants', 'تعداد مشارکت کنندگان کمتر از حداقل مورد نیاز است.');
            }
        });
    }

    /**
     * Determine if the election type requires stock validation.
     *
     * @param  string  $electionType
     * @return bool
     */
    private function electionTypeRequiresStock($electionType)
    {
        // Check for election types that require stock validation
        return in_array($electionType, [
            \App\Enums\ElectionType::PRIVATE_JOINT,
            \App\Enums\ElectionType::PRIVATE_JOINT_WITH_88,
        ]);
    }
}
