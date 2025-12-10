<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Camera extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'ip_address',
        'image',
        'port',
        'app_id',
        'api_key',
        'api_token',
        'description',
        'showroom_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function showroom()
    {
        return $this->belongsTo(Showroom::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'camera_user')->withTimestamps();
    }
}
