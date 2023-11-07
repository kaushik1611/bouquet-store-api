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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('cart_id');
            $table->integer('quantity');
            $table->double('item_amount', 8, 2)->nullable();
            $table->double('item_discount', 8, 2)->nullable();
            $table->double('sale_tax', 8, 2)->nullable();
            $table->double('shipping_charge', 8, 2)->nullable();
            $table->double('packing_charge', 8, 2)->nullable();
            $table->double('sub_total', 8, 2)->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
