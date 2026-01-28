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
        'description',
        'image',
        'showroom_id',
        'is_active',
        'device_id',
        'channel_id',
        'bind_status',
        'enable',
        'security_code',
        'device_model',
        'total_channels',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'bind_status' => 'boolean',
        'enable' => 'boolean',
    ];

    public function showroom()
    {
        return $this->belongsTo(Showroom::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'camera_user')->withTimestamps();
    }

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }
}
