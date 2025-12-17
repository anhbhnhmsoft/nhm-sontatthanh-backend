<?php

namespace App\Http\Resources;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
                'logo' => Storage::disk('public')->url($this->showroom->logo) ?? null,
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
            'avatar' => Storage::disk('public')->url($this->avatar) ?? null,
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
