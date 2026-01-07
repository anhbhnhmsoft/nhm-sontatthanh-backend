<?php

namespace App\Http\Resources;

use App\Core\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ShowroomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $hotlines = $this->hotlines;
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'email' => $this->email,
            'logo' => Storage::disk('public')->url($this->logo) ?? null,
            'hotlines' => array_map(fn($hotline) => [
                'id' => (string) Helper::getTimestampAsId(),
                'name' => $hotline['label'] ?? '',
                'phone' => $hotline['phone'] ?? '',
            ], $hotlines),
        ];
    }
}
