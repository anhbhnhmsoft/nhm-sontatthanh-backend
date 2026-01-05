<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
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
            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => (string) $this->brand->id,
                    'name' => $this->brand->name,
                ];
            }),
            'line' => $this->whenLoaded('line', function () {
                return [
                    'id' => (string) $this->line->id,
                    'name' => $this->line->name,
                ];
            }),
            'colors' => $this->colors,
            'specifications' => $this->specifications,
            'features' => $this->features,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'sell_price' => $this->sell_price,
            'price_discount' => $this->price_discount,
            'discount_percent' => $this->discount_percent,
            'images' => array_map(function ($image) {
                return Storage::disk('public')->url($image);
            }, $this->images),
            'is_active' => $this->is_active,
            'in_stock' => $this->quantity > 0,
        ];
    }
}
