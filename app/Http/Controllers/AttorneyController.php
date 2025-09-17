<?php

namespace App\Http\Controllers;

use App\DTOs\Attorney\CreateAttorneyDto;
use App\DTOs\Attorney\DeleteAttorneyDto;
use App\Http\Requests\Attorney\StoreAttorneyRequest;
use App\Models\Participant;
use App\Models\User;
use App\Services\AttorneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttorneyController extends Controller
{
    protected AttorneyService $attorneyService;

    public function __construct(AttorneyService $attorneyService)
    {
        $this->attorneyService = $attorneyService;
    }
    //get attorney by ajax only by knowing their phone
    //to user not see all the user exist in database
    //only see the user that he search for
    public function getAttorney(Request $request)
    {
        try {
            $data = $request->validate([
                'phone' => 'required|string|max:15',
            ]);
            $users = User::where($data)->select('first_name', 'last_name', 'phone')
                ->first();
            if (!$users) {
                return response()->json(['error' => 'User not found'], 200);
            }
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User not found'], 200);
        }
    }

    public function storeAttorney(StoreAttorneyRequest $request)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();
            $participant = Participant::where('id', $request->input('participant_id'))
                ->first();
            $dto = new CreateAttorneyDto(new User($data), $participant);
            $created = $this->attorneyService->create($dto);

            DB::commit();
            return response()->json(['status' => 'success',
                'message' => 'وکیل با موفقیت ایجاد شد.', 'data' => $created, 200]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error',
                'message' => 'خطایی هنگام ایجاد وکیل رخ داد.', 200]);
//            'message' => $e->getMessage(), 200]);
        }

    }

    public function deleteAttorney(Participant $participant)
    {
        DB::beginTransaction();
        try {
            $dto = new DeleteAttorneyDto($participant->attorney);
            $id=$participant->attorney->id;
            $participant->attorney_id=null;
            $participant->save();
            $deleted = $this->attorneyService->delete($dto);
            DB::commit();
            if ($deleted) {
                return response()->json(['status' => 'success',
                    'message' => 'وکیل با موفقیت حذف شد.','data'=>$id, 200]);
            } else {
                return response()->json(['status' => 'error',
                    'message' => 'خطایی هنگام حذف وکیل رخ داد.', 200]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error',
                'message' => 'خطایی هنگام حذف وکیل رخ داد.', 200]);

        }
    }

}
