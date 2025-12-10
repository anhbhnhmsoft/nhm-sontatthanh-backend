<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'division_type',
        'province_code',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function wards()
    {
        return $this->hasMany(Ward::class, 'district_code', 'code');
    }

    public function showrooms()
    {
        return $this->hasMany(Showroom::class, 'district_code', 'code');
    }
}
