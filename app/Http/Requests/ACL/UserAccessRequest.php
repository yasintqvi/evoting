<?php

namespace App\Http\Requests\ACL;

use App\DTOs\ACL\UserAccessDto;
use Illuminate\Foundation\Http\FormRequest;

class UserAccessRequest extends FormRequest
{
    public function rules(): array
    {

        return [
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ];
    }

    public function toDto(): UserAccessDto
    {
        return new UserAccessDto(
            $this->validated('permissions', []),
            $this->validated('roles', []),
        );
    }
}
