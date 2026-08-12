<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
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
                'image_url' => $product['image_url'] ?? null,
                'active' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($products) . ' produits mis à jour.',
        ]);
    }

    /**
     * Get all products for the mobile app, grouped by menu categories.
     * Products without a matching menu category are excluded.
     */
    public function index()
    {
        $menuCategories = MenuCategory::orderBy('sort_order')->get();
        $products = Product::where('active', true)->orderBy('name')->get();

        $result = [];
        foreach ($menuCategories as $menuCat) {
            $matching = $products->filter(fn ($p) => in_array($p->category, $menuCat->product_categories));
            foreach ($matching as $product) {
                $result[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => round($product->price / 100, 2),
                    'category' => $menuCat->name,
                    'image_url' => $product->image_url ? url('/compress') . '?url=' . urlencode($product->image_url) : null,
                ];
            }
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    /**
     * Get menu categories with icons for the mobile app.
     */
    public function getCategories()
    {
        $categories = MenuCategory::orderBy('sort_order')->get()->map(fn ($cat) => [
            'key' => strtolower(str_replace(' ', '_', $cat->name)),
            'label' => $cat->name,
            'icon' => $cat->icon,
        ]);

        return response()->json(['success' => true, 'data' => $categories]);
    }
}
