<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'type', 'value', 'min_quantity', 'min_cart_value', 'is_product_level'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'voucher_product', 'voucher_id', 'product_id');
    }
}
