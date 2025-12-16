<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'image' => $this->image,
            'type' => $this->type,
            'source' => $this->source,
            'published_at' => $this->published_at?->toIso8601String(),
            'is_active' => $this->is_active,
            'view_count' => $this->view_count ?? 0,
        ];
    }
}
