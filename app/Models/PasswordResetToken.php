<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';
    protected $primaryKey = 'phone';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'phone',
        'token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
