<?php

namespace App\Http\Controllers;

use App\DTOs\Attorney\CreateAttorneyDto;
use App\Http\Requests\Attorney\StoreAttorneyRequest;
use App\Models\Participant;
use App\Models\User;
use App\Models\Vote;
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
            if (! $users) {
                return response()->json(
                    [
                        'error' => __('messages.user.user_not_found'),
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
                'error' => __('messages.user.user_not_found'),
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
                    'message' => __('messages.attorneys.cannot_self'),
                ], 400);
            }

            $dto = new CreateAttorneyDto(new User($data), $participant);
            $created = $this->attorneyService->create($dto);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.attorneys.created'),
                'data' => $created,
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
                'message' => __('messages.attorneys.error'),
            ], 500);
        }
    }

    public function deleteAttorney(Participant $participant)
    {
        DB::beginTransaction();
        try {
            $principalParticipant = Participant::query()->findOrFail($participant->id);
            $principalParticipant->load('attorney');
            $attorneyParticipant = $principalParticipant->attorney;

            if (! $attorneyParticipant) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.attorneys.error'),
                ], 422);
            }

            if ((int) $principalParticipant->id === (int) $attorneyParticipant->id) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.attorneys.error'),
                ], 422);
            }

            if ((int) $principalParticipant->attorney_id !== (int) $attorneyParticipant->id) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.attorneys.error'),
                ], 422);
            }

            $event = $principalParticipant->event;

            $stillLinkedCount = Participant::query()
                ->where('attorney_id', $attorneyParticipant->id)
                ->count();

            if ($stillLinkedCount !== 1) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'لغو وکالت برای چند موکل هم‌زمان از این مسیر پشتیبانی نمی‌شود.',
                ], 422);
            }

            Vote::reassignAllFromParticipantTo((int) $attorneyParticipant->id, (int) $principalParticipant->id);

            $returnNormal = (int) $principalParticipant->delegated_normal_stock_count;
            $returnPrefered = (int) $principalParticipant->delegated_prefered_stock_count;

            if ($returnNormal === 0 && $returnPrefered === 0) {
                $returnNormal = (int) $attorneyParticipant->normal_stock_count;
                $returnPrefered = (int) $attorneyParticipant->prefered_stock_count;
            } else {
                $returnNormal = min($returnNormal, (int) $attorneyParticipant->normal_stock_count);
                $returnPrefered = min($returnPrefered, (int) $attorneyParticipant->prefered_stock_count);
            }

            $principalParticipant->normal_stock_count += $returnNormal;
            $principalParticipant->prefered_stock_count += $returnPrefered;
            $principalParticipant->delegated_normal_stock_count = 0;
            $principalParticipant->delegated_prefered_stock_count = 0;
            $principalParticipant->attorney_id = null;
            $principalParticipant->save();

            $attorneyParticipant->normal_stock_count = max(0, (int) $attorneyParticipant->normal_stock_count - $returnNormal);
            $attorneyParticipant->prefered_stock_count = max(0, (int) $attorneyParticipant->prefered_stock_count - $returnPrefered);
            $attorneyParticipant->save();

            /*
             * رکورد شرکت‌کنندهٔ وکیل را اینجا حذف نمی‌کنیم: همان رکورد ممکن است هم برای سهام‌دار باشد
             * و حذف آن کاربر را از رویداد می‌اندازد. رکورد با سهام صفر می‌ماند؛ موکل سهامش را پس می‌گیرد.
             */

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.attorneys.deleted'),
                'data' => [
                    'principal_participant_id' => $principalParticipant->id,
                    'attorney_participant_id' => $attorneyParticipant->id,
                    'removed_attorney_row' => false,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting attorney', [
                'participant_id' => $participant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.attorneys.error'),
            ], 500);
        }
    }
}
