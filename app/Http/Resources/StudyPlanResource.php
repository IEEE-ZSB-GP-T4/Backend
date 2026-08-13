<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudyPlanResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'available_hours' => (float) $this->available_hours,
            'generated_plan'  => $this->generated_plan,
            'created_at'      => $this->created_at,
        ];
    }
}