<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryNews extends Model
{
    protected $table = 'categories_news';

    protected $fillable = [
        'name',
        'description',
    ];

    public function news()
    {
        return $this->hasMany(News::class, 'category_id');
    }
}
