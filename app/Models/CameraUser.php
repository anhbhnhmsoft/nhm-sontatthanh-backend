<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CameraUser extends Model
{
    use HasFactory;

    protected $table = 'camera_user';

    protected $fillable = [
        'camera_id',
        'user_id',
    ];

    public function camera()
    {
        return $this->belongsTo(Camera::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
