<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CameraResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => Storage::disk('public')->url($this->image) ?? null,
            'is_active' => $this->is_active,
            'device_id' => $this->device_id,
            'channel_id' => $this->channel_id,
            'enable' => $this->enable,
        ];
    }
}
