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
use Log;

class AttorneyController extends Controller
{
    protected AttorneyService $attorneyService;

    public function __construct(AttorneyService $attorneyService)
    {
        $this->attorneyService = $attorneyService;
    }

    // get attorney by ajax only by knowing their phone
    // to user not see all the user exist in database
    // only see the user that he search for
    public function getAttorney(Request $request)
    {
        try {
            $data = $request->validate([
                'phone' => 'required|string|max:15',
            ]);
            $users = User::where($data)->select('first_name', 'last_name', 'phone')
                ->first();
            if (!$users) {
                return response()->json(
                    [
                        'error' => __('messages.user.user_not_found')
                    ],
                    200
                );
            }

            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Error fetching user by phone', [
                'phone' => $request->input('phone'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => __('messages.user.user_not_found')
            ], 200);
        }
    }

    public function storeAttorney(StoreAttorneyRequest $request)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();
            $participant = Participant::where('id', $request->input('participant_id'))
                ->first();
            if ($participant->user->phone == $data['phone']) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.attorneys.cannot_self')
                ], 400);
            }

            $dto = new CreateAttorneyDto(new User($data), $participant);
            $created = $this->attorneyService->create($dto);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.attorneys.created'),
                'data' => $created
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating attorney', [
                'participant_id' => $request->input('participant_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.attorneys.error')
            ], 500);
        }
    }

    public function deleteAttorney(Participant $participant)
    {
        DB::beginTransaction();
        try {
            $dto = new DeleteAttorneyDto($participant->attorney);
            $id = $participant->attorney->id;
            $participant->attorney_id = null;
            $participant->save();
            $deleted = $this->attorneyService->delete($dto);
            DB::commit();
            if ($deleted) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('messages.attorneys.deleted'),
                    'data' => $id
                ], 200);

            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.attorneys.error')
                ], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting attorney', [
                'participant_id' => $participant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.attorneys.error')
            ], 500);
        }
    }
}
