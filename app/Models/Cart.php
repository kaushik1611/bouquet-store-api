<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'cart';
    protected $fillable = ['user_id', 'voucher_id', 'total_item_amount', 'total_discount', 'total_tax', 'total_shipping', 'total_packing	','created_at','updated_at'];

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
