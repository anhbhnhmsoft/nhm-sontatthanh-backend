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
            $department = $this->department ? [
                'id' => (string) $this->department->id,
                'name' => $this->department->name,
            ] : null;
            $showroom = $this->showroom ? [
                'id' => (string) $this->showroom->id,
                'name' => $this->showroom->name,
                'address' => $this->showroom->address,
                'phone' => $this->showroom->phone,
                'email' => $this->showroom->email,
                'logo' => $this->showroom->logo ? Storage::disk('public')->url($this->showroom->logo) : null,
            ] : null;

            $collaborators = $this->collaborators->map(function ($customer) {
                return UserResource::make($customer)->only(['id', 'name', 'phone', 'email']);
            });
            $sale = null;
        } else {
            $department = null;
            $showroom = null;
            $collaborators = null;
            $sale = $this->sale ? [
                'id' => (string) $this->sale->id,
                'name' => $this->sale->name,
                'phone' => $this->sale->phone,
            ] : null;
        }
        return [
            'id' => (string) $this->id ?? null,
            'name' => $this->name ?? null,
            'email' => $this->email ?? null,
            'phone' => $this->phone ?? null,
            'avatar' => str($this->avatar)->startsWith(['http://', 'https://'])
                ? $this->avatar
                : ($this->avatar ? Storage::disk('public')->url($this->avatar) : null),
            'referral_code' => $this->referral_code ?? null,
            'role' => $this->role,
            'joined_at' => $this->joined_at,
            'is_active' => $this->is_active,
            'department_id' => $this->department_id ?? null,
            'department' =>  $department,
            'showroom' => $showroom,
            'manage_sale' => $collaborators,
            'sale' => $sale,
        ];
    }
}
