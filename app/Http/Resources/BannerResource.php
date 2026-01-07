<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
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
            'is_active' => $this->is_active,
            'image' => Storage::disk('public')->url($this->image) ?? null,
            'position' => $this->position,
            'source' => $this->source ?? null,
        ];
    }
}
