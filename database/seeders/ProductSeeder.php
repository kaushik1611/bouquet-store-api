<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed three sample products
        Product::create([
            'name' => 'Product A',
            'description' => 'Sample description for Product A',
            'price' => 10.00,
            'shipping_charge' => 2.00,
            'packing_charge' => 1.00,
            'sale_tax' => 1.20,
            'image_url'=> 'https://www.floraqueen.com/_ipx/q_100&s_480x480/https://fq-products.s3.eu-west-1.amazonaws.com/480x480/63e7658fb028e875274407.jpg'
        ]);

        Product::create([
            'name' => 'Product B',
            'description' => 'Sample description for Product B',
            'price' => 8.00,
            'shipping_charge' => 1.50,
            'packing_charge' => 0.75,
            'sale_tax' => 0.96,
            'image_url' => 'https://d1mxm3s28igxxe.cloudfront.net/480x480wp/631f16ca05215931498711.webp'
        ]);

        Product::create([
            'name' => 'Product C',
            'description' => 'Sample description for Product C',
            'price' => 12.00,
            'shipping_charge' => 2.50,
            'packing_charge' => 1.25,
            'sale_tax' => 1.44,
            'image_url' => 'https://d1mxm3s28igxxe.cloudfront.net/480x480wp/63a19913a09c7753614334.webp'
        ]);
    }
}
