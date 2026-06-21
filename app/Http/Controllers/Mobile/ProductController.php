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

        Product::truncate(); // On vide toute la DB et on remet que les produits encore là
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

    private const CATEGORY_MAP = [
        'Bières bouteilles' => 'bottle',
        'Bières Pression' => 'draft',
        'Café & Thé' => 'bulk',
        'Chips' => 'chips',
        'Jus de fruits' => 'juice',
        'Repas' => 'meal',
        'Softs Alternatifs' => 'soft',
        'Softs Classiques' => 'soft',
        'Viennoiserie' => 'viennoiserie',
        'Vrac' => 'bulk',
        'Saucisson et fromages' => 'bulk',
    ];

    /**
     * Get all products for the mobile app, with categories mapped to app format.
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
                    'price' => $product->price / 100,
                    'category' => self::CATEGORY_MAP[$product->category] ?? $product->category,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
