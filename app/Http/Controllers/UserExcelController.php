<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserExcelController extends Controller
{
    public function uplodeExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);
    
        try {
            Excel::import(new UsersImport, $request->file('file'));
            return redirect()->back()->with('success', 'کاربران با موفقیت وارد شدند.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return redirect()->back()->withErrors($e->failures());
        }
    }
}
