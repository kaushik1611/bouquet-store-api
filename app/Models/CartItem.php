<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id','product_id', 'quantity','item_discount', 'sale_tax', 'shipping_charge', 'packing_charge', 'sub_total', 'item_amount'];
    
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    
}
