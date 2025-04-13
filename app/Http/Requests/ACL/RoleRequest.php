<?php

namespace App\Http\Requests\ACL;

use App\DTOs\ACL\RoleDto;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function rules(): array
    {
        $roleId = $this->route('role')?->id;

        return [
            'name' => [
                'required',
                'string',
                'unique:roles,name' . ($roleId ? ',' . $roleId : '')
            ],
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ];
    }

    public function toDto(): RoleDto
    {
        return new RoleDto(
            $this->validated('name'),
            $this->validated('permissions', [])
        );
    }
}
