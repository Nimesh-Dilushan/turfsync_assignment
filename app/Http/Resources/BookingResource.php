<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_date' => $this->booking_date->toDateString(),
            'start_time' => substr($this->start_time, 0, 5),
            'end_time' => substr($this->end_time, 0, 5),
            'total_price' => (float) $this->total_price,
            'status' => $this->status,
            'facility' => new FacilityResource($this->whenLoaded('facility')),
            'addons' => $this->whenLoaded('addons', function () {
                return $this->addons->map(fn ($addon) => [
                    'id' => $addon->id,
                    'name' => $addon->name,
                    'price' => (float) $addon->price_per_session,
                    'quantity' => $addon->pivot->quantity,
                ]);
            }),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}