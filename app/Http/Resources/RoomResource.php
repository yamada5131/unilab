<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'capacity' => $this->resource->capacity,
            'status' => $this->resource->status,
            'computers' => ComputerResource::collection($this->whenLoaded('computers')),
        ];
    }
}
