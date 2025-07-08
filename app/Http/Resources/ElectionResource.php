<?php

namespace App\Http\Resources;

use App\Enums\ElectionStatus;
use App\Enums\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ElectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalParticipants = $this->participants->count();

        $presentCount = $this->participants->where('is_present', true)->count();

        $participantPercent = $totalParticipants > 0
            ? ($presentCount / $totalParticipants) * 100
            : 0;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'participant_percent' => $participantPercent,
            'type' => $this->type,
            'fa_type' => $this->type->toFa(),
            'status' => $this->status,
            'fa_status' => $this->status->toFa(),
            'main_member_count' => $this->main_member_count,
            'substitute_member_count' => $this->substitute_member_count,
            'normal_stock_count' => $this->normal_stock_count,
            'incpector_main_member_count' => $this->incpector_main_member_count,
            'incpector_substitute_member_count' => $this->incpector_substitute_member_count,
            'quorum_required' => $this->quorum_required,
            'prefered_stock_count' => $this->prefered_stock_count,
            'prefered_stock_weight' => $this->prefered_stock_weight,
            'operations' => [
                'show' => route('elections.show', [$this->company->slug, $this->event->id, $this->id]),
                'edit' => route('elections.edit', [$this->company->slug, $this->event->id, $this->id]),
                'update' => route('elections.update', [$this->company->slug, $this->event->id, $this->id]),
                'delete' => route('elections.delete', [$this->company->slug, $this->event->id, $this->id]),
                'next_step' => $this->getNextStep()
            ],
            'supervisor_id' => $this->supervisor_id,
            'created_at' => verta($this->created_at)->format("Y/m/d H:i"),
            'updated_at' => verta($this->updated_at)->format("Y/m/d H:i"),
            'participants' => $this->participants()->where('is_present', 1)->get(),
            'rounds' => $this->whenLoaded('rounds', fn() => $this->rounds)
        ];
    }

    private function getNextStep(): ?array
    {
        if ($this->status == ElectionStatus::CREATED && user()->hasPermissionTo(Permission::CREATE_CANDIDATES->value)) {
            return [
                "title" => 'تعیین یا تغییر نامزد ها',
                "url" => route('candidates.edit', [$this->company->slug, $this->event->id, $this->id])
            ];
        }

        if ($this->status == ElectionStatus::PARTICIPANTS_ATTENDEES && user()->hasPermissionTo(Permission::CREATE_ATTENDANCE->value)) {
            return [
                "title" => 'حضور و غیاب و ثبت وکالت انتخاباتی',
                "url" => route('attendances.create', [$this->company->slug, $this->event->id, $this->id])
            ];
        }

        if ($this->status == ElectionStatus::WAITING_TO_START && user()->hasPermissionTo(Permission::CREATE_ELECTION_ROUNDS->value)) {
            return [
                "title" => 'شروع انتخابات',
                "url" => route('election-rounds.store', [$this->company->slug, $this->event->id, $this->id]),
            ];
        }

        return null;
    }
}
