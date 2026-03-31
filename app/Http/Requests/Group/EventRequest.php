<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'logo' => ['file', 'mimes:jpg,png,jpeg,webp'],
            'quorum_percent' => ['required', 'numeric', 'min:1', 'max:100'],
        ];
    }
}
