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
            'grid_rows' => $this->resource->grid_rows,
            'grid_cols' => $this->resource->grid_cols,
            'machines' => MachineResource::collection($this->whenLoaded('machines')),
        ];
    }
}
