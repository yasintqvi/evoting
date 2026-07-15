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
            if (!$users) {
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

            if ($participant->is_present) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.attorneys.already_present'),
                ], 400);
            }

            // کسی که خودش وکیل دیگران است نمی‌تواند وکالت بدهد (زنجیرهٔ وکالت پشتیبانی نمی‌شود).
            if (Participant::where('attorney_id', $participant->id)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.attorneys.principal_is_attorney'),
                ], 400);
            }

            // کسی که خودش وکالت داده، سهامی برای رأی ندارد و نمی‌تواند وکیل شود.
            $attorneyUser = User::where('phone', $data['phone'])->first();
            if (
                $attorneyUser &&
                Participant::where('event_id', $participant->event_id)
                    ->where('user_id', $attorneyUser->id)
                    ->whereNotNull('attorney_id')
                    ->exists()
            ) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.attorneys.attorney_has_delegated'),
                ], 400);
            }

            $dto = new CreateAttorneyDto(new User($data), $participant);
            $created = $this->attorneyService->create($dto);

            DB::commit();

            $isNewUser = $created[2] ?? false;
            $user = $created[3] ?? null;
            $cleanPhone = preg_replace('/\D/', '', $data['phone'] ?? '');
            $password = $isNewUser ? substr($cleanPhone, -4) : null;

            return response()->json([
                'status' => 'success',
                'message' => __('messages.attorneys.created'),
                'data' => $created,
                'is_new_user' => $isNewUser,
                'login_info' => $isNewUser ? [
                    'full_name' => $user->full_name ?? $user->first_name . ' ' . $user->last_name,
                    'phone' => $user->phone,
                    'password' => $password,
                    'attorney_name' => $created[0]->user->full_name,
                ] : null,
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

            if (!$attorneyParticipant) {
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

            // اگر وکیل موکل فعال دیگری هم داشته باشد، رأی‌هایش متعلق به همه است و نباید به این موکل منتقل شود.
            $hasOtherActivePrincipals = Participant::query()
                ->where('attorney_id', $attorneyParticipant->id)
                ->where('id', '!=', $principalParticipant->id)
                ->exists();

            if (!$hasOtherActivePrincipals) {
                Vote::reassignAllFromParticipantTo((int) $attorneyParticipant->id, (int) $principalParticipant->id);
            }

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

            $group = $event?->group;
            $attorneyGroupPivot = $group?->users()
                ->where('users.id', $attorneyParticipant->user_id)
                ->first()?->pivot;

            $hasAttorneyStockInGroup = Participant::query()
                ->where('group_id', $group?->id)
                ->where('user_id', $attorneyParticipant->user_id)
                ->where(function ($query) {
                    $query->where('normal_stock_count', '>', 0)
                        ->orWhere('prefered_stock_count', '>', 0)
                        ->orWhere('delegated_normal_stock_count', '>', 0)
                        ->orWhere('delegated_prefered_stock_count', '>', 0);
                })
                ->exists();

            if (
                $group &&
                $attorneyGroupPivot &&
                (int) $attorneyGroupPivot->normal_stock_count === 0 &&
                (int) $attorneyGroupPivot->prefered_stock_count === 0 &&
                !$hasAttorneyStockInGroup
            ) {
                $group->users()->detach($attorneyParticipant->user_id);
            }

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
