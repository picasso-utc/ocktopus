<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Receive products from Bach and store them.
     * Protected by BACH_API_KEY in .env
     */
    public function receiveFromBach(Request $request)
    {
        $apiKey = $request->header('X-Bach-Api-Key');

        if (!$apiKey || $apiKey !== config('services.bach.api_key')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $products = $request->input('products', []);

        if (empty($products)) {
            return response()->json(['success' => false, 'message' => 'No products provided'], 400);
        }

        // Clear existing products and insert new ones
        Product::truncate();

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'price' => $product['price'],
                'category' => $product['category'],
                'active' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($products) . ' produits mis à jour.',
        ]);
    }

    /**
     * Get all products for the mobile app.
     */
    public function index()
    {
        $products = Product::where('active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price / 100, // Convert cents to euros
                    'category' => $product->category,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
