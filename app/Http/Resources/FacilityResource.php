<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacilityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sport_type' => $this->sport_type,
            'hourly_rate' => (float) $this->hourly_rate,
            'description' => $this->description,
            'is_active' => (bool) $this->is_active,
        ];
    }
}