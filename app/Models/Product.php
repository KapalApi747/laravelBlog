<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'discount_price', 'stock_quantity'];

    public function photos() {
        return $this->belongsToMany(Photo::class);
    }
    public function categories() {
        return $this->morphToMany(Category::class, 'categoryable');
    }
}
