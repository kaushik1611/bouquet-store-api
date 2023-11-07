<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Http\Requests\VoucherRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     *
     * Get cart items data
     * @return mixed
     */
    public function getCart()
    {
        // get car data
        $cart = Cart::with(['cartItems.product', 'voucher'])->where('user_id', 1)->first();
        $vouchers = Voucher::get(); 
        $voucherData = [];
        $isApplied = 0;
        foreach ($vouchers as $voucher) {
            // check if applied then pass flag 1
            $isApplied = (!empty($cart->voucher_id) && $voucher->id == $cart->voucher_id) ? 1 : 0;
            if ($voucher->is_product_level) {

                if (!empty($cart)) {
                    // get products which have specific voucher 
                    $voucherProducts = $voucher->products->pluck('id')->all();
                    //get cart items 
                    $cartProductIds = $cart->cartItems->pluck('product_id')->toArray();
                    // Retrieve cart items eligible for coupon code application.
                    $applicableProductIds = array_intersect($cartProductIds, $voucherProducts);
                    $applicableItems = $cart->cartItems->whereIn('product_id', $applicableProductIds);
                    if (count($applicableItems)) {
                        $discount = ($voucher->type == 'fixed') ? $voucher->value . '€' : $voucher->value . '%';
                        $applicableDiscount = 0;
                        $applicableCode = 0;
                        foreach ($applicableItems as $applicableItem) {
                            if ($voucher->min_quantity && $applicableItem->quantity >= $voucher->min_quantity) {
                                $applicableCode = 1;
                                $applicableDiscount += $this->calculateItemDiscount($voucher, $applicableItem->item_amount, $applicableItem->quantity);
                            }
                        }
                        $sign = ($voucher->type == 'fixed') ? '€' : '%';
                        $discount = ($applicableDiscount) ? $applicableDiscount : $discount;
                        $voucherData[] = [
                            'code' => $voucher->code,
                            'status' => $applicableCode,
                            'savings' => $discount. $sign,
                            'label' => 'Get upto ' . $discount . '€ discount.',
                            'is_applied' => $isApplied
                        ];
                    } else {
                        // If other items are present but are not eligible for the voucher, they should not be applied.
                        $discount = ($voucher->type == 'fixed') ? $voucher->value . '€' : $voucher->value . '%';
                        $voucherData[] = [
                            'code' => $voucher->code,
                            'status' => 0,
                            'savings' => $discount,
                            'label' => 'Get upto ' . $discount . ' discount on above ' . $voucher->min_quantity . ' tems.',
                            'is_applied' => $isApplied
                        ];
                    }
                } else {
                    // If the cart is empty, no voucher should be applied.
                    $discount = ($voucher->type == 'fixed') ? $voucher->value . '€' : $voucher->value . '%';
                    $voucherData[] = [
                        'code' => $voucher->code,
                        'status' => 0,
                        'savings' => $discount,
                        'label' => 'Get upto ' . $discount . ' discount on above ' . $voucher->min_quantity . ' tems.',
                        'is_applied' => $isApplied
                    ];
                }
            } elseif ($voucher->min_cart_value) {
                //If the voucher is designed to be applied when the cart total before applying charges matches, then it should be applied.
                $discount = ($voucher->type == 'fixed') ? $voucher->value . '€' : $voucher->value . '%';
                $voucherData[] = [
                    'code' => $voucher->code,
                    'status' => (!empty($cart->cartItems) && $cart->total_item_amount >= $voucher->min_cart_value) ? 1 : 0,
                    'savings' => $discount,
                    'label' => 'Get upto ' . $discount . ' off on cart value ' . $voucher->min_cart_value . '€ and above.',
                    'is_applied' => $isApplied
                ];
            } else {
                // If the voucher is eligible with no conditions, it should be applied if the cart contains items.
                $discount = ($voucher->type == 'fixed') ? $voucher->value . '€' : $voucher->value . '%';
                $voucherData[] = [
                    'code' => $voucher->code,
                    'status' => (!empty($cart->cartItems)) ? 1 : 0,
                    'savings' => $discount,
                    'label' => 'Get upto ' . $discount . ' discount.',
                    'is_applied' => $isApplied
                ];
            }
        }
        // prepaer respone data
        $cartData = [
            'cart' => [
                'convenience_fee' => [
                    'delivery_fee' => $cart->total_shipping ?? 0,
                    'packaging_fee' => $cart->total_packing ?? 0,
                    'sale_tax' => $cart->total_tax ?? 0,
                ],
                'discount' => [
                    'total_savings' => $cart->total_discount ?? 0,
                ],
                'grand_total' => $cart->grand_total ?? 0,
                'cart_items' => $cart->cartItems ?? []
            ],
            'voucher' => $voucherData
        ];
        return response()->json([
            'status' => 'success',
            'message' => 'Cart items retrieved',
            'data' => $cartData,
        ]);
    }

    /**
     *
     * Itmes add to cart
     *
     * @param  CartRequest $request
     * @return mixed
     *
     */
    public function addToCart(CartRequest $request)
    {
        $product_id = $request->input('product_id');
        $quantity = $request->input('quantity');
        //TBD:: manage user as per login session , for now we have taken static user id
        $user_id = 1;

        $product = Product::find($product_id);
        $cart = Cart::where('user_id', 1)->first();
        // create cart object if cart is empty
        if (empty($cart)) {
            $cart = Cart::create(['user_id' => 1]);
        }
        // update or create cart items
        $cartItem = $this->updateOrCreateCartItem($cart, $product, $quantity);
        return response()->json([
            'status' => 'success',
            'message' => 'Item added to the cart',
            'data' => $cartItem,
        ]);
    }

    /**
     *
     * Update cart data
     *
     * @param  $cart
     * @return mixed
     *
     */
    public function updateCart($cart)
    {
        $cart->refresh(); // refresh cart data after updating voucher id updated or removed
        $allCartItems = CartItem::selectRaw('SUM(quantity*item_amount) as sub_item_amount, SUM(item_amount) as total_items_amount, SUM(sale_tax) as total_sale_tax, SUM(shipping_charge) as total_shipping_charge, SUM(packing_charge) as total_packing_charge, SUM(item_discount) as total_item_discount')->where(['cart_id' => $cart->id])->first();
        if (empty($allCartItems->sub_item_amount)) {
            //if cart has no item then main cart id should be deleted.
            Cart::where('user_id', 1)->delete();
            return false;
        }
        $total_discount = 0;
        // calculate total discount
        if (isset($cart->voucher) && !$cart->voucher->is_product_level) {
            if ($cart->voucher->min_cart_value) {
                if ($allCartItems->sub_item_amount >= $cart->voucher->min_cart_value) {
                    $total_discount = $this->calculateDiscountOnType($cart->voucher, $allCartItems->sub_item_amount);
                }
            } else {
                $total_discount = $this->calculateDiscountOnType($cart->voucher, $allCartItems->sub_item_amount);
            }
        } else {
            $total_discount = $allCartItems->total_item_discount;
        }
        // update cart total values
        $cart->total_item_amount = $allCartItems->sub_item_amount;
        $cart->total_discount = $total_discount;
        $cart->total_tax = $allCartItems->total_sale_tax;
        $cart->total_shipping = $allCartItems->total_shipping_charge;
        $cart->total_packing = $allCartItems->total_packing_charge;
        $cart->grand_total = ($allCartItems->sub_item_amount - $total_discount) + $allCartItems->total_sale_tax + $allCartItems->total_shipping_charge + $allCartItems->total_packing_charge;
        $cart->save();
        return $cart;
    }

    /**
     *
     * Update or create cart items and calculate discount
     *
     * @param  $cart
     * @param  $product
     * @param  $quantity
     * @return mixed
     *
     */
    public function updateOrCreateCartItem($cart, $product, $quantity)
    {
        $discount = 0;
        if (!empty($cart->voucher_id)) {
            $voucher = Voucher::find($cart->voucher_id);
            if ($voucher->is_product_level) {
                $discount = $this->calculateItemDiscount($voucher, $product->price, $quantity);
            }
        }
        $sub_total = (($product->price * $quantity) - $discount) + $product->sale_tax + $product->shipping_charge + $product->packing_charge;
        $cartItem = CartItem::updateOrCreate(['cart_id' => $cart->id, 'product_id' => $product->id], [
            'quantity' => $quantity,
            'item_discount' => $discount,
            'item_amount' => $product->price,
            'sale_tax' => $product->sale_tax,
            'shipping_charge' => $product->shipping_charge,
            'packing_charge' => $product->packing_charge,
            'sub_total' => $sub_total,
        ]);
        $this->updateCart($cart);
        return $cartItem;
    }

    /**
     *
     * Calculate discount based on type Ie : percentage or fixed
     *
     * @param  $voucher
     * @param  $amount
     * @return mixed
     *
     */
    public function calculateDiscountOnType($voucher, $amount)
    {
        if ($voucher->type == 'fixed') {
            $discount = $voucher->value;
        } else {
            $discount = ($amount * ($voucher->value / 100)) ?? 0;
        }
        return $discount;
    }

    /**
     *
     * Calculate discount item level IE: we have voucher that applied based on product qty
     *
     * @param  $voucher
     * @param  $price
     * @param  $quantity
     * @return mixed
     *
     */
    public function calculateItemDiscount($voucher, $price, $quantity)
    {
        $total_discount = 0;
        if ($voucher->min_quantity) {
            $discountableQty = $quantity - ($voucher->min_quantity - 1);
            $discountableAmount = $price * $discountableQty;
            $total_discount = $this->calculateDiscountOnType($voucher, $discountableAmount);
        } else {
            $discountableAmount =  $price * $quantity;
            $total_discount = $this->calculateDiscountOnType($voucher, $discountableAmount);
        }
        return $total_discount;
    }

    /**
     *
     * Item remove from cart
     *
     * @param  Request $request
     * @return mixed
     *
     */
    public function removeFromCart(Request $request)
    {
        // Find the cart item by its ID
        $cartItem = CartItem::with(['cart'])->where('product_id', $request->product_id)->first();
        if (!empty($cartItem)) {
            $cart = $cartItem->cart;
            
            // Remove the item from the cart
            $cartItem->delete();
            $cart = $this->updateCart($cart);
            if ($cart && !empty($cart->voucher_id)) {
                $voucher = Voucher::find($cart->voucher_id);
                if ($voucher) {
                    if ($voucher->is_product_level) {
                        $applicableItems = $cart->cartItems->where('product_id', $request->product_id);
                        if ($applicableItems) {
                            $this->updateCartVoucherId($cart);
                        }
                    } elseif ($voucher->min_cart_value) {
                        if ($cart->total_item_amount < $voucher->min_cart_value) {
                            $this->updateCartVoucherId($cart);
                        }
                    }
                }
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Item removed from the cart',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'The item has already been removed.',
            ]);
        }
    }

    /**
     *
     * calculate and update discount item level when user applies voucher
     *
     * @param  $car
     * @param  $voucher
     * @param  $applicableItems
     * @param  $applicableProductLevel
     * @return mixed
     *
     */
    public function applyVoucherItemLevel($cart, $voucher, $applicableItems, $applicableProductLevel)
    {
        foreach ($applicableItems as $applicableItem) {

            if ($voucher->min_quantity && $applicableItem->quantity >= $voucher->min_quantity) {

                $applicableProductLevel = 1;
                if ($cart->voucher_id != $voucher->id) {
                    $this->updateCartVoucherId($cart, $voucher);
                }
                $this->updateOrCreateCartItem($cart, $applicableItem->product, $applicableItem->quantity);
            }
        }
        return $applicableProductLevel;
    }

    /**
     *
     * Apply voucher
     *
     * @param  VoucherRequest $request
     * @return mixed
     *
     */
    public function applyVoucher(VoucherRequest $request)
    {
        $cart = Cart::with('cartItems')->where('user_id', 1)->first();
        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please add items to cart before applying voucher.',
                'data' => null,
            ], 400);
        }
        $voucherCode = $request->input('code');
        $voucher = Voucher::where('code', $voucherCode)->first();
        if ($voucher->is_product_level) {
            $applicableProductLevel = 0;
            // get cart items
            $cartProductIds = $cart->cartItems->pluck('product_id')->toArray();
            //check if cart items is eligible for particular voucher
            if ($voucher->products->whereIn('id', $cartProductIds)->pluck('id')->count()) {
                $voucherProducts = $voucher->products->pluck('id')->all();
                $cartProductIds = $cart->cartItems->pluck('product_id')->toArray();
                $applicableProductIds = array_intersect($cartProductIds, $voucherProducts);
                $applicableItems = $cart->cartItems->whereIn('product_id', $applicableProductIds);
                $applicableProductLevel = $this->applyVoucherItemLevel($cart, $voucher, $applicableItems, $applicableProductLevel);
            }
            if (!$applicableProductLevel) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This voucher is valid for a specific product when you order a minimum ' . $voucher->min_quantity . ' Items.',
                    'data' => null,
                ], 400);
            }
        } else {
            if ($voucher->min_cart_value) {
                if ($cart->total_item_amount >= $voucher->min_cart_value) {
                    $this->updateCartVoucherId($cart, $voucher);
                    $this->updateCart($cart);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'This voucher is valid for a cart value exceeding $' . $voucher->min_cart_value . '.',
                        'data' => null,
                    ], 400);
                }
            } else {
                $this->updateCartVoucherId($cart, $voucher);
                $this->updateCart($cart);
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Voucher applied successfully',
            'data' => $voucher,
        ]);
    }

    /**
     *
     * update voucher to cart item
     *
     * @param  $cart
     * @param  $voucher
     * @return mixed
     *
     */

    public function updateCartVoucherId($cart, $voucher = null)
    {
        $cart->voucher_id = $voucher->id ?? null;
        $cart->save();
        $this->updateCart($cart);
        return $cart;
    }
}
