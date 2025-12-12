<?php

namespace App\Http\Resources;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->role == UserRole::SALE->value) {
            $deparment = $this->department;
            $managedSales = $this->managedSales;
            $cameras = $this->cameras;
        } else {
            $deparment = null;
            $managedSales = null;
            $cameras = null;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'referral_code' => $this->referral_code,
            'role' => $this->role,
            'joined_at' => $this->joined_at,
            'is_active' => $this->is_active,
            'department_id' => $this->department_id,
            'sale_id' => $this->sale_id,
            'department' => $deparment,
            'managed_sales' => $managedSales,
            'cameras' => $cameras,
        ];
    }
}
