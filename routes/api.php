<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('bouquets')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
});

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'getCart']);
    Route::post('/add', [CartController::class, 'addToCart']);
    Route::post('/remove', [CartController::class, 'removeFromCart']);
    Route::post('/voucher/apply', [CartController::class, 'applyVoucher']);
});


