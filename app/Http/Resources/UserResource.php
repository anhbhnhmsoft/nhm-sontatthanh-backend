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
        if ($this->role == UserRole::SALE->value) {
            $department = [
                'id' => (string) $this->department->id,
                'name' => $this->department->name,
            ];
            $showroom = [
                'id' => (string) $this->showroom->id,
                'name' => $this->showroom->name,
                'address' => $this->showroom->address,
                'phone' => $this->showroom->phone,
                'email' => $this->showroom->email,
                'logo' => $this->showroom->logo,
            ];
        } else {
            $department = null;
            $showroom = null;
        }
        return [
            'id' => (string) $this->id,
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
            'department' =>  $department ?? null,
            'showroom' => $showroom ?? null,
        ];
    }
}
