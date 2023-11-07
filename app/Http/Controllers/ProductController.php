<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ]);
    }


    public function show(Product $product)
    {
        if ($product) {
            return response()->json([
                'status' => 'success',
                'message' => 'Product retrieved successfully',
                'data' => $product,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
                'data' => null,
            ], 404); 
        }
    }

    
}
