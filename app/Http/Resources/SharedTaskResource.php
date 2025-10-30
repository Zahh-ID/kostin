<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\SharedTask
 *
 * @OA\Schema(
 *     schema="SharedTaskResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="property_id", type="integer"),
 *     @OA\Property(property="property", ref="#/components/schemas/PropertyResource"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="rrule", type="string", nullable=true),
 *     @OA\Property(property="next_run_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="assignee_user_id", type="integer", nullable=true),
 *     @OA\Property(property="assignee", ref="#/components/schemas/UserResource"),
 *     @OA\Property(property="logs", type="array", @OA\Items(ref="#/components/schemas/SharedTaskLogResource")),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 */
class SharedTaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'property' => new PropertyResource($this->whenLoaded('property')),
            'title' => $this->title,
            'description' => $this->description,
            'rrule' => $this->rrule,
            'next_run_at' => $this->next_run_at?->toIso8601String(),
            'assignee_user_id' => $this->assignee_user_id,
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'logs' => SharedTaskLogResource::collection($this->whenLoaded('logs')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
