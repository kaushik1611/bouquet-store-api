<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'voucher_product', 'product_id', 'voucher_id');
    }
}
