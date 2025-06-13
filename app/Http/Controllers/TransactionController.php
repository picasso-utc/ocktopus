<?php 

namespace App\Http\Controllers;

use App\Models\Articles;
use App\Models\MarketPrices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function handle(Request $request)
    {
        $badgeId = $request->input('badge_id');
        $items = $request->input('items'); // [[article_id, quantity], ...]
        $sessionId = $request->input('session_id');

        $systemId = env('WEEZEVENT_SYSTEM_ID');
        $appKey = env('WEEZEVENT_APP_KEY');
        $fundId = env('WEEZEVENT_FUND_ID');

        $mappedItems = [];
        foreach ($items as [$articleId, $quantity]) {
            $categoryId = $this->getCategoryFromArticle($articleId);
            if ($categoryId == 11) {    // Si c'est une bière pression, on remplace par le prix du marché
                $firstTransaction = Http::withHeaders([  // Première transaction de l'article (qui aura été mis à 0e) pour les stats
                    'accept-language' => 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
                ])->post('https://api.nemopay.net/services/POSS3/transaction?system_id='.$systemId.'&app_key='.$appKey.'&sessionid='.$sessionId, [
                    'badge_id' => $badgeId,
                    'obj_ids' => [[$articleId, $quantity]],
                    'fun_id' => $fundId,
                ])->throw();

                $currentPrice = $this->getCurrentMarketPrice($articleId);
                $replacementArticleId = $this->mapPriceToBeerArticle($currentPrice);
                $mappedItems[] = [$replacementArticleId, $quantity];
                $this->updateMarket($articleId, $quantity);
            } else {
                $mappedItems[] = [$articleId, $quantity];
            }
        }

        $response = Http::withHeaders([
            'accept-language' => 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
        ])->post('https://api.nemopay.net/services/POSS3/transaction?system_id='.$systemId.'&app_key='.$appKey.'&sessionid='.$sessionId, [
            'badge_id' => $badgeId,
            'obj_ids' => $mappedItems,
            'fun_id' => $fundId,
        ])->throw();

        return response()->json($response->json()); 
    }

    private function getCategoryFromArticle($articleId)
    {
        $article = Articles::where('article_id', $articleId)->first() ?? null;
        return $article ? $article->category_id : null;
    }

    private function getCurrentMarketPrice($articleId)
    {
        return MarketPrices::where('article_id', $articleId)
            ->first()
            ->price ?? 1.0;
    }

    private function mapPriceToBeerArticle($price)
    {
        $rounded = number_format(round($price / 0.05) * 0.05, 2, '.', '');
        $priceToId = [
            '0.60' => 23597,
            '0.65' => 20445,
            '0.70' => 23598,
            '0.75' => 20446,
            '0.80' => 23599,
            '0.85' => 21003,
            '0.90' => 23600,
            '0.95' => 20682,
            '1.00' => 23601,
            '1.05' => 23596,
            '1.10' => 23602,
            '1.15' => 23648,
            '1.20' => 23603, 
            '1.25' => 23649,
            '1.30' => 23604,
            '1.35' => 23650,
            '1.40' => 23605,
        ];
        return $priceToId[$rounded] ?? 23601;
    }

    private function updateMarket($articleId, $quantity)
    {
        $maxPrice = 1.4;
        $minPrice = 0.6;
        $priceStep = 0.01;
        // $fluctuationRange = 0.05;

        $article = Articles::where('article_id', $articleId)->first();
        if (!$article) return;

        $categoryId = $article->category_id;

        $allArticles = Articles::where('category_id', $categoryId)->get();
        $nbArticles = $allArticles->count();

        foreach ($allArticles as $otherArticle) {
            $currentPrice = MarketPrices::firstOrNew(
                ['article_id' => $otherArticle->article_id]
            );

            if (!$currentPrice->exists) {
                $currentPrice->price = 1.00;
            }

            $newPrice = $currentPrice->price;

            if ($otherArticle->article_id == $articleId) {
                $newPrice += $priceStep * $quantity;
            } else {
                $newPrice -= ($priceStep * $quantity) / ($nbArticles - 1);
            }

            $newPrice = max($minPrice, min($maxPrice, $newPrice));
            $currentPrice->price = $newPrice;
            $currentPrice->updated_at = now();
            $currentPrice->save();
        }
    }

    public function getPrices()
    {
        $articles = MarketPrices::all();

        foreach ($articles as $article) {
            $articleModel = Articles::where('article_id', $article->article_id)->first();
            $article->article_name = $articleModel ? $articleModel->article_name : null;
        }

        return $articles;
    }
}
