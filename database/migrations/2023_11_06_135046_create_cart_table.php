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
        Schema::create('cart', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->double('total_item_amount', 8, 2)->nullable();
            $table->double('total_discount', 8, 2)->nullable();
            $table->double('total_tax', 8, 2)->nullable();
            $table->double('total_shipping', 8, 2)->nullable();
            $table->double('total_packing', 8, 2)->nullable();
            $table->double('grand_total', 8, 2)->nullable();
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};
