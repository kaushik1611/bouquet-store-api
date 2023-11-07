<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponProduct extends Model
{
    protected $table = 'coupon_product';
    public $timestamps = false;

    protected $fillable = ['coupon_id', 'product_id'];

    // Define relationships
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
