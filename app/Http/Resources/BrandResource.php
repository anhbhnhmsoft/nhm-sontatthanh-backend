<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BrandResource extends JsonResource
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
            'logo' => Storage::disk('public')->url($this->logo) ?? null,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'source' => $this->source ?? null,
        ];
    }
}
