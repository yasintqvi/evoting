<?php

namespace App\Http\Requests\Election;

use Illuminate\Foundation\Http\FormRequest;

class StoreVotingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'director_candidates' => ['nullable', 'array'],
            'inspector_candidates' => ['nullable', 'array'],
            
        ];
    }
}
