<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed three sample vouchers
        // Seed data for 3 vouchers
        Voucher::create([
            'code' => 'VoucherV',
            'type' => 'percentage',
            'value' => 10,
            'min_quantity' => 2, // Applies to Product A,
            'is_product_level'=>1
        ]);

        Voucher::create([
            'code' => 'VoucherR',
            'type' => 'fixed',
            'value' => 5,
        ]);

        Voucher::create([
            'code' => 'VoucherS',
            'type' => 'percentage',
            'value' => 5,
            'min_cart_value' => 40,
        ]);
    }
}
