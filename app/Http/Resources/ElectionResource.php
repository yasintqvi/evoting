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
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'type' => $this->type->value,
            'fa_type' => $this->type->toFa(),
            'status' => $this->status,
            'fa_status' => $this->status->toFa(),
            'main_member_count' => $this->main_member_count,
            'substitute_member_count' => $this->substitute_member_count,
            'normal_stock_count' => $this->normal_stock_count,
            'incpector_main_memberphp_count' => $this->incpector_main_member_count,
            'incpector_substitute_member_count' => $this->incpector_substitute_member_count,
            'quorum_required' => $this->quorum_required,
            'prefered_stock_count' => $this->prefered_stock_count,
            'prefered_stock_weight' => $this->prefered_stock_weight,
            'position' => $this->position->title,
            'position_id' => $this->position_id,
            'operations' => [
                'show' => route('elections.show', [$this->event->group->slug, $this->event->slug, $this->slug]),
                'edit' => route('elections.edit', [$this->event->group->slug, $this->event->slug, $this->slug]),
                'update' => route('elections.update', [$this->event->group->slug, $this->event->slug, $this->id]),
                'delete' => route('elections.delete', [$this->event->group->slug, $this->event->slug, $this->slug]),
                'create_duplicate' => route('elections.create', [
                    'group' => $this->event->group->slug,
                    'event' => $this->event->slug,
                    'duplicate_from' => $this->slug,
                ]),
                'next_step' => $this->getNextStep(),
            ],
            'template_block' => $this->templateBlockForCopy(),
            'created_at' => verta($this->created_at)->format('Y/m/d H:i'),
            'updated_at' => verta($this->updated_at)->format('Y/m/d H:i'),
            'starts_at' => $this->starts_at ? verta($this->starts_at)->format('Y/m/d H:i') : null,
            'ends_at' => $this->ends_at ? verta($this->ends_at)->format('Y/m/d H:i') : null,
            'ends_at_raw' => $this->ends_at?->toDateTimeString(),
            'is_expired' => $this->isExpired(),
            'rounds' => $this->whenLoaded('rounds', fn () => $this->rounds),
            'has_votes' => $this->votes()->exists(),
            'report_url' => route('elections.report', [$this->event->group->slug, $this->event->slug, $this->slug]),
            'can_delete' => ! $this->status->isImmutableStatuses(),
        ];
    }

    private function getNextStep(): ?array
    {
        if ($this->status == ElectionStatus::CREATED && user()->hasPermissionTo(Permission::CREATE_CANDIDATES->value)) {
            return [
                'title' => 'تعیین یا تغییر نامزد ها',
                'url' => route('candidates.edit', [$this->event->group->slug, $this->event->slug, $this->slug]),
                'method' => 'GET',
            ];
        }

        if ($this->status == ElectionStatus::WAITING_TO_START && user()->hasPermissionTo(Permission::CREATE_ELECTION_ROUNDS->value)) {
            return [
                'title' => 'شروع انتخابات',
                'url' => route('elections.start', [$this->event->group->slug, $this->event->slug, $this->slug]),
                'method' => 'POST',
                'action' => 'start',
            ];
        }

        if (
            $this->status == ElectionStatus::ONGOING
            && user()->hasPermissionTo(Permission::CREATE_ELECTION_ROUNDS->value)
        ) {
            return [
                'title' => 'پایان انتخابات',
                'url' => route('elections.end', [$this->event->group->slug, $this->event->slug, $this->slug]),
                'method' => 'POST',
                'action' => 'end',
            ];
        }

        return null;
    }
}
