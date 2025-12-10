<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'brand_id',
        'line_id',
        'colors',
        'specifications',
        'features',
        'quantity',
        'price',
        'sale_price',
        'is_active',
    ];

    protected $casts = [
        'colors' => 'array',
        'specifications' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
