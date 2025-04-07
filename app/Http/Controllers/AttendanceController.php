<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Company $company){

        return view('app.company.attendances.index', compact('company'));

    }
}
