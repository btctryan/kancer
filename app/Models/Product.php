<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'price',
        'weight',
        'image',
        'stock',
        'is_active',
    ];

    // Relasi ke Cart
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}