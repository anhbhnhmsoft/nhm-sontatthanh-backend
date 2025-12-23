<?php

namespace App\Models;

use App\Core\GenerateId\HasBigIntId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use HasBigIntId, SoftDeletes;

    protected $table = 'channels';

    protected $fillable = [
        'camera_id',
        'status',
        'name',
        'position',
    ];

    public function camera()
    {
        return $this->belongsTo(Camera::class);
    }   
}