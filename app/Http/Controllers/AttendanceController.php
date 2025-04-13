<?php

namespace App\Http\Controllers;

use App\Http\Requests\Election\StoreAttendanceRequest;
use App\Models\Company;
use App\Models\Election;
use App\Services\AttendanceService;
use Exception;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function create(Company $company)
    {
        // $election->load('participants.user');

        return view('app.company.attendances.create', compact('company'));
    }

    public function store(StoreAttendanceRequest $request, Company $company)
    {
        // try {
        //     $this->attendanceService->create($request->toDto(), $election);

        //     return to_route('elections.index', [$company->slug]);
        // } catch (Exception $e) {
        //     return back()->with('error', $e->getMessage());
        // }
    }
}
