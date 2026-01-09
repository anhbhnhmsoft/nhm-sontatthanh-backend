<?php

namespace App\Http\Resources;

use App\Core\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'hotlines' => array_map(fn($hotline) => [
                'id' => (string) Helper::getTimestampAsId(),
                'name_user' => $hotline['name_user'] ?? '',
                'name' => $hotline['label'] ?? '',
                'phone' => $hotline['phone'] ?? '',
            ], (array) $this->hotlines) ?? [],
        ];
    }
}
