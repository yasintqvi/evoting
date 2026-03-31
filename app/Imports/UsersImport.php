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
                'nationalcode' => $row[3] ?? null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            '2' => 'required|string|unique:users,phone',
            '3' => 'nullable|string|max:10|unique:users,nationalcode',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '2.unique' => 'این شماره موبایل قبلاً ثبت شده است!',
            '2.required' => 'شماره موبایل الزامی است!',
            '3.unique' => 'این کد ملی قبلاً ثبت شده است!',
            '3.max' => 'کد ملی باید حداکثر ۱۰ رقم باشد!',
        ];
    }
}
