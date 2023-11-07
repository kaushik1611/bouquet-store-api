<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed']);
            $table->integer('value'); // Value of the discount (percentage or fixed amount)
            $table->integer('min_quantity')->nullable(); // Minimum quantity for discount to apply
            $table->integer('min_cart_value')->nullable(); // Minimum cart value for discount to apply
            $table->enum('is_product_level',[0,1])->default(0); // voucher for particular products
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
