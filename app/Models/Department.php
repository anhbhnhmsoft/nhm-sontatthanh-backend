<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'hotlines',
    ];

    protected $casts = [
        'hotlines' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'department_id');
    }

    public function showroom()
    {
        return $this->belongsTo(Showroom::class);
    }
}
