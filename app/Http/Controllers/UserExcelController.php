<?php

namespace App\Http\Controllers;

use App\Enums\ElectionStatus;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ParticipantsImport;
use Illuminate\Http\Request;
use App\Models\Election;
use App\Models\Group;

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

    public function import(Request $request, Group $group, Election $election)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            $import = new ParticipantsImport($group->id, $election->id);
            Excel::import($import, $request->file('file'));

            $election->status = $election->quorum_required ?  ElectionStatus::PARTICIPANTS_ATTENDEES : ElectionStatus::WAITING_TO_START;

            $election->save();

            return to_route('elections.index', $group->slug)
                ->with('success', 'سهام‌داران با موفقیت وارد شدند.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
