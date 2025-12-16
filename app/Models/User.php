<?php

namespace App\Models;

use App\Core\GenerateId\HasBigIntId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasBigIntId, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'referral_code',
        'role',
        'joined_at',
        'is_active',
        'department_id',
        'showroom_id',
        'password',
        'email_verified_at',
        'phone_verified_at',
        'sale_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'joined_at' => 'datetime',
            'is_active' => 'boolean',
            'role' => 'integer',
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function sale()
    {
        return $this->belongsTo(User::class, 'sale_id');
    }

    public function cameras()
    {
        return $this->belongsToMany(Camera::class, 'camera_user')->withTimestamps();
    }

    public function managedSales()
    {
        return $this->hasMany(User::class, 'sale_id');
    }

    public function showroom () {
        return $this->belongsTo(Showroom::class, 'showroom_id');
    }
}
