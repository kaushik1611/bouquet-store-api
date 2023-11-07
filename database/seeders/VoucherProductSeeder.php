<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoucherProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Associate vouchers with products
        $productA = Product::where('name', 'Product A')->first();
        $productB = Product::where('name', 'Product B')->first();
        $productC = Product::where('name', 'Product C')->first();

        $voucherV = Voucher::where('code', 'VoucherV')->first();
        $voucherR = Voucher::where('code', 'VoucherR')->first();
        $voucherS = Voucher::where('code', 'VoucherS')->first();

        // Associate vouchers with products as needed
        $productA->vouchers()->attach($voucherV->id);
        // You can associate vouchers with other products as required
    }
}
