<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToCollection, WithValidation
{
    public function collection(Collection $rows)
    {
        $rows = $rows->slice(1);

        foreach ($rows as $row) {
            User::create([
                'first_name' => $row[0],
                'last_name' => $row[1],
                'phone' => $row[2],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            '2' => 'required|string|unique:users,phone',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '2.unique' => 'این شماره موبایل قبلاً ثبت شده است!',
            '2.required' => 'شماره موبایل الزامی است!',
        ];
    }
}
