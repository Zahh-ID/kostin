<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\SharedTaskLog
 *
 * @OA\Schema(
 *     schema="SharedTaskLogResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="shared_task_id", type="integer"),
 *     @OA\Property(property="shared_task", ref="#/components/schemas/SharedTaskResource"),
 *     @OA\Property(property="run_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="completed_by", type="integer", nullable=true),
 *     @OA\Property(property="completed_by_user", ref="#/components/schemas/UserResource"),
 *     @OA\Property(property="photo_url", type="string", nullable=true),
 *     @OA\Property(property="note", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 */
class SharedTaskLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shared_task_id' => $this->shared_task_id,
            'shared_task' => new SharedTaskResource($this->whenLoaded('sharedTask')),
            'run_at' => $this->run_at?->toIso8601String(),
            'completed_by' => $this->completed_by,
            'completed_by_user' => new UserResource($this->whenLoaded('completedBy')),
            'photo_url' => $this->photo_url,
            'note' => $this->note,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
