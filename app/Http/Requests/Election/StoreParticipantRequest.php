<?php

namespace App\Http\Requests\Election;

use Illuminate\Foundation\Http\FormRequest;

class StoreParticipantRequest extends FormRequest
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
            $rules['participants.*.normal_stock_count'] = 'required|integer|min:0';
            $rules['participants.*.prefered_stock_count'] = 'required|integer|min:0';
        }

        return $rules;
    }

    private function electionTypeRequiresStock($electionType)
    {
        return in_array($electionType, [
            \App\Enums\ElectionType::PRIVATE_JOINT,
            \App\Enums\ElectionType::PRIVATE_JOINT_WITH_88
        ]);
    }
}
