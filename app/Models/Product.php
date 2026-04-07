<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'badge',
        'stock',
        'weight',   // berat domba dalam kg
        'is_active',
    ];

    // Relasi ke Cart
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}