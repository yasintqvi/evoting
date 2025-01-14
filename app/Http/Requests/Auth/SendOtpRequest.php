<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Otp\SendOtpDTO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class SendOtpRequest extends FormRequest
{
    public function prepareForValidation()
    {
        if ($this->has('phone')) {
            $this->merge([
                'identifier' => ed($this->identifier),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'regex:/^(\+98|0)?9\d{9}$/'],
        ];
    }

    public function toDTO()
    {
        return new SendOtpDTO(
            $this->identifier
        );
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors(),
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
